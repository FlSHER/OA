import {Icon, Input, Button, Row, Col, message} from 'antd';
import '../../public/dingtalk';
import Form from '../modules/OAForm';

const FormItem = Form.Item;

class NormalLoginForm extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Row>
                <Col xs={0} lg={24} style={{height: '60px'}}> </Col>
                <Col xs={0} xl={24} style={{height: '60px'}}> </Col>
                <Col xs={0} xxl={24} style={{height: '60px'}}> </Col>
                <Col span={24} style={{padding: '40px 10px 0'}}>
                    <div style={{margin: '0 auto', maxWidth: '360px', border: 'none'}}>
                        <Form onSuccess={this.onSuccess}>
                            <FormItem style={{textAlign: 'center'}}>
                                <img src="/images/login-logo.png?v=0.0.1" height={70}/>
                                <p style={{fontSize: '18px', color: '#9e9e9e'}}>
                                    让我们一起实现梦想
                                </p>
                            </FormItem>
                            <FormItem name={'old_pwd'}>
                                <Input size="large"
                                       prefix={<Icon type="lock" style={{color: 'rgba(0,0,0,.25)'}}/>}
                                       type="password" placeholder="原密码"/>
                            </FormItem>
                            <FormItem name={'password'}>
                                <Input size="large"
                                       prefix={<Icon type="lock" style={{color: 'rgba(0,0,0,.25)'}}/>}
                                       type="password" placeholder="新密码"/>
                            </FormItem>
                            <FormItem name={'password_confirmation'}>
                                <Input size="large"
                                       prefix={<Icon type="lock" style={{color: 'rgba(0,0,0,.25)'}}/>}
                                       type="password" placeholder="确认新密码"/>
                            </FormItem>
                            <Input name="dingding" type="hidden"/>
                            <FormItem>
                                {/*<a style={{float: 'right'}} href="">忘记密码</a>*/}
                                <Button type="primary" size="large" htmlType="submit" style={{width: '100%'}}>
                                    登 录
                                </Button>

                            </FormItem>
                        </Form>
                    </div>
                </Col>
            </Row>
        );
    }

    onSuccess(response) {
        location.href = response.url;
    }

}

if (document.getElementById('view')) {
    ReactDOM.render(<NormalLoginForm/>, document.getElementById('view'));
}


