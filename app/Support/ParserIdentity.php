<?php

namespace App\Support;

class ParserIdentity
{
    /**
     * 中国大陆身份证号码
     *
     * @var string
     */
    protected $idNumber;
    /**
     * 中国大陆身份证号码长度
     *
     * @var int
     */
    protected $idLength;
    /**
     * 身份证号码是否验证通过
     *
     * @var bool
     */
    protected $isValidate = false;
    /**
     * 加权因子
     *
     * @var array
     */
    protected $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    /**
     * 校验码
     *
     * @var array
     */
    protected $verifyCode = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];


    public function __construct(string $idNumber)
    {
        $idNumber = str_replace(' ', '', $idNumber);
        $idNumber = str_replace('-', '', $idNumber);
        $idNumber = str_replace('_', '', $idNumber);

        $this->idNumber = strtoupper(trim($idNumber));
        $this->idLength = strlen($this->idNumber);
        $this->isValidate = false;
    }

    /**
     * 验证身份证合法性.
     * 
     * @return boolean. 
     */
    public function isValidate(): bool
    {
        if ($this->isValidate) {
            return true;
        }

        if ($this->checkFormat() && $this->checkBirth() && $this->checkLastCode() && $this->idLength == 18) {
            $this->isValidate = true;

            return true;
        }

        return false;
    }

    /**
     * 获取生日信息.
     * 
     * @return string
     */
    public function birthday(): string
    {
        if (!$this->isValidate || !$this->isValidate()) {
            return false;
        }

        $year = substr($this->idNumber, 6, 4);
        $month = substr($this->idNumber, 10, 2);
        $day = substr($this->idNumber, 12, 2);

        return sprintf('%s-%s-%s', $year, $month, $day);
    }

    /**
     * 根据身份证信息获取其性别
     *
     * @return bool|string
     */
    public function gender(): string
    {
        if (!$this->isValidate || !$this->isValidate()) {
            return false;
        }

        return ((intval(substr($this->idNumber, 16, 1)) % 2) === 0) ? '女' : '男';
    }

    /**
     * 通过正则表达式检测身份证号码格式
     *
     * @return bool
     */
    protected function checkFormat(): bool
    {
        $this->id15To18();

        $pattern = '/^\d{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}$/';

        if ($this->idLength == 18) {
            $pattern = '/^\d{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/';
        }

        return (bool) preg_match($pattern, $this->idNumber);
    }

    /**
     * 检测身份证生日是否正确
     *
     * @return bool
     */
    protected function checkBirth(): bool
    {
        $year = substr($this->idNumber, 6, 4);
        $month = substr($this->idNumber, 10, 2);
        $day = substr($this->idNumber, 12, 2);
        
        return (bool) checkdate($month, $day, $year);
    }

    /**
     * 校验身份证号码最后一位校验码
     *
     * @return bool
     */
    protected function checkLastCode(): bool
    {
        if ($this->idLength == 15) {
            return true;
        }
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += substr($this->idNumber, $i, 1) * $this->factor[$i];
        }
        $mod = $sum % 11;

        return ($this->verifyCode[$mod] === substr($this->idNumber, -1));
    }

    /**
     * 将 15 位身份证转化为 18 位身份证号码
     *
     * @return string
     */
    protected function id15To18()
    {
        if ($this->idLength == 15) {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($this->idNumber, 12, 3), ['996', '997', '998', '999']) !== false) {
                $this->idNumber = substr($this->idNumber, 0, 6) . '18' . substr($this->idNumber, 6, 9);
            } else {
                $this->idNumber = substr($this->idNumber, 0, 6) . '19' . substr($this->idNumber, 6, 9);
            }

            // 补全最后一位
            
        }

        return $this->idNumber;
    }
}