import {Grid, NavBar, Popover, Icon} from 'antd-mobile';

class Entrance extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <NavBar
                    rightContent={[
                        <Popover
                            key="right"
                            overlay={[
                                (<Popover.Item action="backstage">进入后台</Popover.Item>),
                                (<Popover.Item action="reset_password">重置密码</Popover.Item>),
                                (<Popover.Item action="logout">退出登录</Popover.Item>)
                            ]}
                            onSelect={this.selectPopover}
                        >
                            <div><Icon type="ellipsis"/></div>
                        </Popover>
                    ]}
                >应用目录</NavBar>
                <Grid data={[]} hasLine={false}/>
            </div>
        );
    }

    componentBeforeMount() {

    }

    componentDidMount() {

    }

    selectPopover(item) {
        switch (item.props.action) {
            case "backstage":
                location.href = "/";
                break;
            case "reset_password":
                location.href = "/reset_password?redirect_uri=" + encodeURI('/entrance');
                break;
            case "logout":
                location.href = "/logout";
                break;
        }
    }

    getApps() {
        axios.post()
    }
}


if (document.getElementById('view')) {
    ReactDOM.render(<Entrance/>, document.getElementById('view'));
}


