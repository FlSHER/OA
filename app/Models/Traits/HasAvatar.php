<?php 

namespace App\Models\Traits;

use Intervention\Image\Image;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\FilesystemManager;

trait HasAvatar
{
    /**
     * 获取唯一key.
     *
     * @return string|int
     */
    abstract public function getAvatarKey(): string;

    /**
     * 获取文件类型.
     *
     * @return array
     */
    public function getAvatarExtensions(): array
    {
        return ['svg', 'png', 'jpg', 'jpeg', 'gif', 'bmp'];
    }

    /**
     * 获取路径前缀.
     *
     * @return string
     */
    public function getAvatarPrefix(): string
    {
        return 'avatars';
    }

    /**
     * 获取头像 url.
     *
     * @param int $size
     * @param string $prefix
     * @return mixed
     */
    public function avatar(int $size = 0, string $prefix = '')
    {
        $filename = $this->avatarPath($prefix);

        if (! $filename) {
            return null;
        }

        $extra = $size ? [
        	'width' => $size,
        	'height' => $size,
        ] : [];

        return $this->validateProcessAnd($filename, $extra, function (Image $image, array $extra = []) use ($filename) {
            if ($extra['blur']) {
                $image->blur($extra['blur']);
            }

            $this->processSize($image, $extra);

            $quality = intval($extra['quality'] ?? 90) ?: 90;
            $quality = min($quality, 90);

            $image->encode($image->extension, $quality);

            return $this->putProcessFile(
                $image,
                $this->makeProcessFilename($filename, $this->makeProcessFingerprint($extra))
            );
        });
    }

    /**
     * 验证文件是否需要处理，如果需要则执行回调.
     *
     * @param string $filename
     * @param array $extra
     * @param callable $call
     * @return string
     */
    private function validateProcessAnd(string $filename, array $extra, callable $call): string
    {
        $width = floatval($extra['width'] ?? 0.0);
        $height = floatval($extra['height'] ?? 0.0);
        $quality = intval($extra['quality'] ?? 0);
        $blur = intval($extra['blur'] ?? 0);

        if (! $width && ! $height && ! $quality && ! $blur) {
            return $this->makeUrl($filename);
        }

        return $this->validateFingerprint($filename, $call, [
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
            'blur' => $blur,
        ]);
    }

    /**
     * 处理文件尺寸.
     *
     * @param \Intervention\Image\Image $image
     * @param array $extra
     * @return void
     */
    protected function processSize(Image $image, array $extra)
    {
        $width = $image->width();
        $height = $image->height();

        $processWidth = floatval($extra['width']);
        $processHeight = floatval($extra['height']);

        if (($width <= $processWidth || $height <= $processHeight) || (! $processWidth && ! $processHeight)) {
            return;
        }

        $minSide = min($processWidth, $processHeight);

        if (($minSide === $processWidth && $processWidth) || ((bool) $processWidth && ! $processHeight)) {
            $image->resize($processWidth, null, function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        } elseif (($minSide === $processHeight && $processWidth) || ((bool) $processHeight && ! $processWidth)) {
            $image->resize(null, $processHeight, function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        }
    }

	/**
     * 验证文件,文件存在，则直接返回，否则执行回调.
     *
     * @param string $filename
     * @param callable $call
     * @param array $extra
     * @return string
     */
    private function validateFingerprint(string $filename, callable $call, array $extra): string
    {
        $processFilename = $this->makeProcessFilename($filename, $this->makeProcessFingerprint($extra));

        $disk = $this->filesystem()->disk(
            config('filesystems.disks.public.visibility')
        );

        if ($disk->exists($processFilename)) {
            return $this->makeUrl($processFilename);
        }

        return $call(
            $this->makeImage(config('filesystems.disks.public.root').'/'.$filename),
            $extra
        );
    }

    /**
     * 保存转换后的文件并返回地址.
     *
     * @param \Intervention\Image\Image $image
     * @param string $filename
     * @return string
     */
    private function putProcessFile(Image $image, string $filename): string
    {
        $disk = $this->filesystem()->disk(
            config('filesystems.disks.public.visibility')
        );

        if (! $image->isEncoded() || ! $disk->put($filename, $image)) {
            throw new \Exception('The file encode error.');
        }

        return $this->makeUrl($filename);
    }

    /**
     * Make Image.
     *
     * @param string $filename
     * @return \Intervention\Image\Image
     */
    protected function makeImage(string $filename): Image
    {
        return app()->make(ImageManager::class)->make($filename);
    }

    /**
     * 生成文件转换信息.
     *
     * @param array $extra
     * @return string
     */
    protected function makeProcessFingerprint(array $extra): string
    {
        return md5(implode('|', array_filter($extra)));
    }

    /**
     * 生成转换后的文件路径.
     *
     * @param string $filename
     * @param string $fingerprint
     * @return string
     */
    protected function makeProcessFilename(string $filename, string $fingerprint): string
    {
        $processPath = str_replace(sprintf('.%s', $ext = pathinfo($filename, PATHINFO_EXTENSION)), '/', $filename);

        return $processPath.$fingerprint.'.'.$ext;
    }

    /**
     * 返回头像全路径.
     *
     * @param string $filename
     * @return string
     */
    protected function makeUrl(string $filename): string
    {
    	$disk = $this->filesystem()->disk(
            config('filesystems.disks.public.visibility')
        );

        return $disk->url($filename);
    }

    /**
     * 获取头像路径.
     *
     * @param string $prefix
     * @return string|null
     */
    public function avatarPath(string $prefix = '')
    {
        $path = $this->makeAvatarPath($prefix);
        $disk = $this->filesystem()->disk(
            config('filesystems.disks.public.visibility')
        );

        foreach ($this->getAvatarExtensions() as $extension) {
            if ($disk->exists($filename = $path.'.'.$extension)) {
                return $filename;
            }
        }

        return null;
    }

    /**
     * 保存员工头像.
     *
     * @param UploadedFile $avatar
     * @return string|false
     */
    public function storeAvatar(UploadedFile $avatar, string $prefix = '')
    {
        $extension = strtolower($avatar->extension());
        if (! in_array($extension, $this->getAvatarExtensions())) {
            throw new \Exception('保存的头像格式不符合要求');
        }
        if ($extension !== 'gif') {
            ini_set('memory_limit', '-1');
            Image::make($avatar->getRealPath())->orientate()->save($avatar->getRealPath(), 100);
        }

        $filename = $this->makeAvatarPath($prefix);
        $path = pathinfo($filename, PATHINFO_DIRNAME);
        $name = pathinfo($filename, PATHINFO_BASENAME).'.'.$extension;
        
        return $avatar->storeAs($path, $name, config('filesystems.disks.public.visibility'));
    }

    /**
     * 生成头像路径（000/000/000）.
     *
     * @return string
     */
    protected function makeAvatarPath(string $prefix = ''): string
    {
        $filename = strval($this->getAvatarKey());
        if (strlen($filename) < 11) {
            $filename = str_pad($filename, 11, '0', STR_PAD_LEFT);
        }

        return sprintf(
            '%s/%s/%s/%s/%s',
            $prefix ?: $this->getAvatarPrefix(),
            substr($filename, 0, 3),
            substr($filename, 3, 3),
            substr($filename, 6, 3),
            substr($filename, 9)
        );
    }

    /**
     *  获取文件系统管理对象.
     *
     * @return \Illuminate\Filesystem\FilesystemManager
     */
    protected function filesystem(): FilesystemManager
    {
        return app(FilesystemManager::class);
    }
}
