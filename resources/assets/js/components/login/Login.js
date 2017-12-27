import {Form, Icon, Input, Button, Card, Row, Col, Spin, message} from 'antd';
import '../../public/dingtalk';

const FormItem = Form.Item;

class NormalLoginForm extends Component {

    constructor(props) {
        super(props);
        this.state = {loading: false};
    }

    render() {
        const {getFieldDecorator} = this.props.form;
        return (
            <Row>
                <Col xs={0} lg={24} style={{height: '60px'}}> </Col>
                <Col xs={0} xl={24} style={{height: '60px'}}> </Col>
                <Col xs={0} xxl={24} style={{height: '60px'}}> </Col>
                <Col span={24} style={{padding: '40px 10px 0'}}>
                    <div style={{margin: '0 auto', maxWidth: '360px', border: 'none'}}>
                        <Spin spinning={this.state.loading}>
                            <Form onSubmit={(e) => this.handleSubmit(e)}>
                                <FormItem style={{textAlign: 'center'}}>
                                    <img src="/images/login-logo.png?v=0.0.1" height={70}/>
                                    <p style={{fontSize: '18px',color:'#9e9e9e'}}>
                                        让我们一起实现梦想
                                    </p>
                                </FormItem>
                                <FormItem>
                                    {getFieldDecorator('username', {
                                        rules: [{required: true, message: '请输入用户名!'}],
                                    })(
                                        <Input size="large"
                                               prefix={<Icon type="user" style={{color: 'rgba(0,0,0,.25)'}}/>}
                                               placeholder="用户名/手机号"/>
                                    )}
                                </FormItem>
                                <FormItem>
                                    {getFieldDecorator('password', {
                                        rules: [{required: true, message: '请输入密码!'}],
                                    })(
                                        <Input size="large"
                                               prefix={<Icon type="lock" style={{color: 'rgba(0,0,0,.25)'}}/>}
                                               type="password" placeholder="密码"/>
                                    )}
                                </FormItem>
                                {getFieldDecorator('dingding', {
                                    initialValue: '',
                                })(
                                    <Input type="hidden"/>
                                )}
                                <FormItem>
                                    {/*<a style={{float: 'right'}} href="">忘记密码</a>*/}
                                    <Button type="primary" size="large" htmlType="submit" style={{width: '100%'}}>
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

    handleSubmit(e) {
        e.preventDefault();
        this.setState({loading: true});
        let formData = this.props.form.getFieldsValue();
        axios.post('', formData).then((response) => {
            let status = response.data.status;
            if (status == 1) {
                location.href = response.data.url;
            } else if (status == -1) {
                message.error(response.data.message);
            }
            setTimeout(() => {
                this.setState({loading: false})
            }, 500);
        }).catch(function (err) {
            this.setState({loading: false});
            message.error('登录异常');
        });
    }

    getDingtalkAuthCode() {
        try {
            this.setState({loading: true});
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
                            } else if (status == -1) {
                                message.error(response.data.message);
                            } else if (status == -2) {
                                message.error(response.data.message);
                                this.props.form.setFieldsValue({dingding: response.data.dingding});
                            }
                            setTimeout(() => {
                                this.setState({loading: false})
                            }, 500);
                        }).catch(function (err) {
                            this.setState({loading: false});
                            message.error('登录异常');
                        });
                    } else {
                        this.setState({loading: false});
                        message.error('无法获取登录授权码，请手动登录');
                    }
                },
                onFail: (err) => {
                    this.setState({loading: false});
                    message.error('获取钉钉授权码失败，请手动登录');
                }
            });
        } catch (e) {
            this.setState({loading: false});
            message.error(e.message);
        }
    }
}

const WrappedNormalLoginForm = Form.create()(NormalLoginForm);

if (document.getElementById('view')) {
    ReactDOM.render(<WrappedNormalLoginForm/>, document.getElementById('view'));
}


