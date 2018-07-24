import { Form, Spin, message } from 'antd';

let OAFormFields;

class OAForm extends Component {

  constructor(props) {
    super(props);
    this.state = {
      loading: false,
      fields: this.getFields(props)
    };
    OAFormFields = this.state.fields;
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    let children = this.props.children;
    return (
      <Spin spinning={this.state.loading} delay={500}>
        <Form {...this.getFormProps(this.props)} >
          {React.Children.map(children, (item) => {
            if (item.type == Form.Item && item.props.name) {
              return <Form.Item {...this.getFormItemProps(item.props)} >
                {getFieldDecorator(item.props.name, {})(
                  item.props.children
                )}
              </Form.Item>
            } else if (item.props.name) {
              return getFieldDecorator(item.props.name)(
                React.cloneElement(item, { key: item.props.name })
              )
            } else {
              return item;
            }
          })}
        </Form>
      </Spin>
    );
  }

  componentDidMount() {
    this.props.bindSelf(this);
  }

  getFields(props) {
    let fields = {};
    let item;
    for (let i in props.children) {
      item = props.children[i];
      if (item.type == Form.Item && item.props.name) {
        fields[item.props.name] = {
          validateStatus: '',
          validateMessage: ''
        }
      }
    }
    return fields;
  }

  getFormProps(props) {
    let formProps = {};
    let propsException = ["children", "form", "action", "onSuccess", "onError", "bindSelf"];
    for (let i in props) {
      if (propsException.indexOf(i) == -1) {
        formProps[i] = props[i];
      }
    }
    formProps.onSubmit = (e) => this.handleSubmit(e);
    return formProps;
  }

  getFormItemProps(props) {
    let name = props.name;
    let formItemProps = {};
    let propsException = ["children", "name"];
    for (let i in props) {
      if (propsException.indexOf(i) == -1) {
        formItemProps[i] = props[i];
      }
    }
    formItemProps.key = name;
    formItemProps.validateStatus = this.state.fields[name].validateStatus;
    formItemProps.help = this.state.fields[name].validateMessage;
    formItemProps.onFoucus = () => {
      console.log('test');
    }
    return formItemProps;
  }

  loading() {
    this.setState({ loading: true });
  }

  loaded() {
    this.setState({ loading: false });
  }

  handleSubmit(e) {
    e.preventDefault();
    this.loading();
    let formData = this.props.form.getFieldsValue();
    axios.post(this.props.action, formData).then((response) => {
      let status = response.data.status;
      if (status == 1) {
        this.props.onSuccess(response.data);
      } else if (status == -1) {
        this.props.onError(response.data);
      } else {
        message.error('非法返回值');
        console.log(response.data);
      }
      this.loaded();
    }).catch((err) => {
      if (err.response && (err.response.status == 422 || err.response.status == 423)) {
        this.showValidateError(err);
      } else if (err.response && err.response.data) {
        message.error(err.response.data.message);
        console.error(err.response.data);
      } else {
        message.error('系统异常');
        console.error(err);
      }
      this.loaded();
    });
  }

  showValidateError(err) {
    let validateError = err.response.data.errors || err.response.data;
    this.setState((prevState) => {
      let prevFields = prevState.fields;
      for (var i in validateError) {
        if (prevFields[i]) {
          prevFields[i].validateStatus = 'error';
          prevFields[i].validateMessage = validateError[i].join(";");
        } else {
          message.error(i + ':' + validateError[i].join(";"));
        }
      }
      return {
        fields: prevFields
      }
    });
  }
}

OAForm.defaultProps = {
  action: "",
  onSuccess: (response) => {
    message.success(response.message);
  },
  onError: (response) => {
    message.error(response.message);
  },
  bindSelf: (self) => {
    //
  }
};

OAForm.Item = Form.Item;

export default Form.create({
  onFieldsChange: (props, fields) => {
    for (let i in fields) {
      if (OAFormFields[i]) {
        OAFormFields[i].validateStatus = "";
        OAFormFields[i].validateMessage = "";
      }
    }
  }
})(OAForm);


