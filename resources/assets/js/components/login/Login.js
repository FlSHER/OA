import { Icon, Input, Button, Row, Col, Spin, message } from 'antd';
import '../../public/dingtalk';
import Form from '../modules/OAForm';

const FormItem = Form.Item;

class LoginForm extends Component {

  constructor(props) {
    super(props);
    this.state = { loading: false };
  }

  render() {
    return (
      <Row>
        <Col xs={0} lg={24} style={{ height: '60px' }}> </Col>
        <Col xs={0} xl={24} style={{ height: '60px' }}> </Col>
        <Col xs={0} xxl={24} style={{ height: '60px' }}> </Col>
        <Col span={24} style={{ padding: '40px 10px 0' }}>
          <div style={{ margin: '0 auto', maxWidth: '360px', border: 'none' }}>
            <Spin spinning={this.state.loading}>
              <Form onSuccess={this.onSuccess} bindSelf={(self) => this.form = self}>
                <FormItem style={{ textAlign: 'center' }}>
                  <img src="/images/login-logo.png?v=0.0.1" height={70} />
                  <p style={{ fontSize: '18px', color: '#9e9e9e' }}>
                    让我们一起实现梦想
                  </p>
                </FormItem>
                <FormItem name="mobile">
                  <Input size="large"
                         prefix={<Icon type="user" style={{ color: 'rgba(0,0,0,.25)' }} />}
                         placeholder="手机号" />
                </FormItem>
                <FormItem name="password">
                  <Input size="large"
                         prefix={<Icon type="lock" style={{ color: 'rgba(0,0,0,.25)' }} />}
                         type="password" placeholder="密码（123456）" />
                </FormItem>
                <Input type="hidden" name="dingding" />
                <FormItem>
                  {/*<a style={{float: 'right'}} href="">忘记密码</a>*/}
                  <Button type="primary" size="large" htmlType="submit" style={{ width: '100%' }}>
                    登 录
                  </Button>
                </FormItem>
              </Form>
            </Spin>
          </div>
        </Col>
      </Row>
    );
  }

  componentDidMount() {
    dd.ready(() => {
      dd.ui.webViewBounce.disable();
      this.getDingtalkAuthCode();
    });
    dd.error((msg) => {
      message.error('钉钉初始化失败，请手动登录');
    });
  }

  onSuccess(response) {
    location.href = response.url;
  }

  getDingtalkAuthCode() {
    try {
      this.setState({ loading: true });
      dd.runtime.permission.requestAuthCode({
        corpId: CorpId,
        onSuccess: (result) => {
          if (result.code) {
            var params = {
              dingtalk_auth_code: result.code
            };
            axios.post('', params).then((response) => {
              let status = response.data.status;
              if (status == 1) {
                location.href = response.data.url;
              } else {
                message.error(response.data.message || '未知错误');
              }
              setTimeout(() => {
                this.setState({ loading: false })
              }, 500);
            }).catch((err) => {
              if (err.response && err.response.data && err.response.data.message) {
                if (err.response.headers.dingding) {
                  this.form.props.form.setFieldsValue({ dingding: err.response.headers.dingding });
                }
                message.error(err.response.data.message);
              } else {
                message.error('自动登录异常:' + err.message);
              }
              this.setState({ loading: false });
            });
          } else {
            this.setState({ loading: false });
            message.error('无法获取登录授权码，请手动登录');
          }
        },
        onFail: (err) => {
          this.setState({ loading: false });
          message.error('获取钉钉授权码失败，请手动登录');
        }
      });
    } catch (e) {
      this.setState({ loading: false });
      message.error(e.message);
    }
  }
}

if (document.getElementById('view')) {
  ReactDOM.render(<LoginForm />, document.getElementById('view'));
}


