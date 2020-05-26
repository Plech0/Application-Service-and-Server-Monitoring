class TableRow extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            active: false,
        }
    }

    ToggleClass = (e) => {
        this.setState({
            active: !this.state.active,
        })
    }

    render() {
        if (typeof (this.props.message.errMsg) === "undefined") {
            return "";
        }
        let levelClass = "";
        if (this.props.message.level.match(/warn.*/g)) {
            levelClass = "warning";
        }
        if (this.props.message.level.match(/err.*/g)) {
            levelClass = "error";
        }
        return (
            <tr
                className={this.state.active ? "active" : "non-active"}
                onClick={this.ToggleClass}
            >
                <td>{this.props.message.time}</td>
                <td>{this.props.message.errCode}</td>
                <td><span className={levelClass}>{this.props.message.level}</span></td>
                <td>{this.props.message.clientIp}</td>
                <td className="msg"><span>{this.props.message.errMsg}</span></td>
            </tr>);
    }
}

class LogOutput extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: [],
            errType: "aplication_log",
            errLevel: "error",
        }
    }

    componentDidMount() {
        this.refreshMessages();
        setInterval(this.refreshMessages, 150000);
    }

    refreshMessages = async () => {
        let data = [];
        if (this.state.errType === "error_apache") {
            data = await getApacheMessages(this.props, this.state.errType, this.state.errLevel);
        } else {
            data = await getAplicationMessages(this.props, this.state.errType, this.state.errLevel);
        }
        this.setState({
            data: data,
        })
    }

    updateErrType = async (event) => {
        const type = await event.target.value;
        this.setState({
            errType: type,
        })
        await this.refreshMessages();
    }

    updateErrLevel = async (event) => {
        const level = await event.target.value;
        this.setState({
            errLevel: level,
        })
        await this.refreshMessages();
    }

    render() {
        let messages = this.state.data.map((message) => {
            return (
                <TableRow message={message}/>
            );
        });
        return (
            <div>
                <div className={'graph-settings'}>
                    <select name="source" onChange={this.updateErrType} value={this.state.errType}>
                        <option value="error_apache">Apache</option>
                        <option value="aplication_log" selected>Aplication</option>
                    </select>

                    <select name="errorLevel" onChange={this.updateErrLevel} value={this.state.errLevel} className="err-level">
                        <option value="emerg">emergenci</option>
                        <option value="alert">alert</option>
                        <option value="crit">critical</option>
                        <option value="error" selected>error</option>
                        <option value="warn">warning</option>
                        <option value="notice">notice</option>
                        <option value="info">info</option>
                        <option value="debug">debug</option>
                        <option value="trace">trace</option>
                    </select>
                </div>
                <table className="table">
                    <thead className="thead-dark">
                    <tr>
                        <th scope="col">Time</th>
                        <th scope="col">Code</th>
                        <th scope="col">Level</th>
                        <th scope="col">IP</th>
                        <th scope="col">Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    {messages}
                    </tbody>
                </table>
            </div>
        )
    }
}

document.querySelectorAll('#messages .apache-error').forEach((domContainer) => {
    ReactDOM.render(
        <LogOutput
            host={domContainer.dataset.host}
            vhost={domContainer.dataset.vhost}
            es={domContainer.dataset.es}
            token={domContainer.dataset.token}
        />, domContainer);
})

async function getApacheMessages(props, type, level) {
    let messages = [];
    const token = props.token;
    const url = `${props.es}/filebeat-${props.host}-*/_search`;
    await axios.post(url,
        {
            query: {
                bool: {
                    must: [
                        {
                            exists: {
                                field: "log_type"
                            }
                        },
                        {
                            term: {
                                "log_type.keyword": {
                                    value: `${type}`
                                }
                            }
                        },
                        {
                            regexp: {
                                "loglevel.keyword": `${level}`
                            }
                        }
                    ]
                }
            },
            sort: [
                {
                    "@timestamp": {
                        order: "desc",
                        unmapped_type: "boolean"
                    }
                }
            ],
            size: 10000
        }, {
            headers: {
                'Authorization': `Basic ${token}`,
            }
        }).then((response) => {
        response.data.hits.hits.map((hit, i) => {
            let time = moment(hit._source['@timestamp']);
            messages[i] = {
                time: time.format("YYYY.MM.DD HH:mm:ss"),
                errCode: hit._source['errorcode'],
                errMsg: hit._source['error_msg'],
                clientIp: hit._source['clientip'],
                level: hit._source['loglevel'],
            }
        });
    }).catch((error) => {
        console.log(error);
    })

    console.log(messages)
    return messages;
}

async function getAplicationMessages(props, type, level) {
    let messages = [];
    const token = props.token;
    const url = `${props.es}/filebeat-${props.host}-*/_search`;
    let domain = props.vhost.split(".");
    domain = domain[0];
    await axios.post(url,
        {
            query: {
                bool: {
                    must: [
                        {
                            exists: {
                                field: "log_type"
                            }
                        },
                        {
                            term: {
                                "log_type.keyword": {
                                    value: `${type}`
                                }
                            }
                        },
                        {
                            regexp: {
                                "loglevel.keyword":  `${level}`
                            }
                        },
                        {
                            term: {
                                "domain.keyword": {
                                    value: `${domain}`
                                }
                            }
                        }
                    ]
                }
            },
            sort: [
                {
                    "@timestamp": {
                        order: "desc",
                        unmapped_type: "boolean"
                    }
                }
            ],
            size: 10000
        }, {
            headers: {
                'Authorization': `Basic ${token}`,
            }
        }).then((response) => {
        response.data.hits.hits.map((hit, i) => {
            let time = moment(hit._source['@timestamp']);
            messages[i] = {
                time: time.format("YYYY.MM.DD HH:mm:ss"),
                errCode: hit._source['category'],
                errMsg: hit._source['error_msg'],
                clientIp: hit._source['clientip'],
                level: hit._source['loglevel'],
            }
        });
    }).catch((error) => {
        console.log(error);
    })

    console.log(messages)
    return messages;
}