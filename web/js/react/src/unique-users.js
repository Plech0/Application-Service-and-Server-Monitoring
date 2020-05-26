const {BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer} = Recharts;


class UniqueBar extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: [],
            time: 6
        };
    }

    componentDidMount() {
        this.refreshGraph();
        setInterval(this.refreshGraph, 300000);
    }

    refreshGraph = async () => {
        let data = await getDataForBar(this.props, this.state.time);
        this.setState({
            data: data,
        })
    }

    updateTimeNumber = async (event) => {
        let time = await parseInt(event.target.value, 10);
        this.setState({
            time: time
        })

        this.refreshGraph();
    }

    render() {
        return (
            <div className={'graph-wrapper'}>
                <div className={'graph-settings'}>
                    <input type="number" value={this.state.time} onChange={this.updateTimeNumber}/> days ago
                </div>
                <ResponsiveContainer width={"100%"} height={300}>
                    <BarChart data={this.state.data}
                              margin={{top: 5, right: 30, left: 20, bottom: 5}}>
                        <CartesianGrid strokeDasharray="3 3"/>
                        <XAxis dataKey="time"/>
                        <YAxis/>
                        <Tooltip/>
                        <Legend/>
                        <Bar dataKey="count" fill="#8884d8" name={"Unique users"}/>
                    </BarChart>
                </ResponsiveContainer>
            </div>
        );
    }

}

function GraphInit(props) {
    return (
        <div>
            <UniqueBar
                name={props.name}
                host={props.host}
                es={props.es}
                vhost={props.vhost}
                token={props.token}
            />
        </div>
    );
}

document.querySelectorAll('#graphs .unique-graph').forEach((domContainer) => {
    ReactDOM.render(
        <GraphInit
            name={domContainer.dataset.name}
            host={domContainer.dataset.host}
            es={domContainer.dataset.es}
            vhost={domContainer.dataset.vhost}
            token={domContainer.dataset.token}
        />, domContainer);
})

async function getDataForBar(props, time) {
    let graphValues = [];
    const token = props.token;
    const url = `${props.es}/filebeat-${props.host}-*/_search`
    let domain = props.vhost.split(".");
    domain = domain[0];
    await axios.post(url,
        {
            size: 0,
            query: {
                bool: {
                    must: [
                        {
                            exists: {
                                field: `${props.name}`
                            }
                        },
                        {
                            exists: {
                                field: "virtualhost"
                            }
                        },
                        {
                            regexp: {
                                "virtualhost.keyword": {
                                    value: `${domain}\..*`
                                }
                            }
                        },
                        {
                            term: {
                                "log_type.keyword": {
                                    value: "access_apache"
                                }
                            }
                        }
                    ],
                    filter: {
                        range: {
                            "@timestamp": {
                                lte: "now",
                                gte: `now-${time - 1}d/d`
                            }
                        }
                    }

                },

            },
            aggs: {
                interval: {
                    date_histogram: {
                        field: "@timestamp",
                        calendar_interval: "day"
                    },
                    aggs: {
                        total_count: {
                            cardinality: {
                                field: `${props.name}`
                            }
                        }
                    }
                }
            }
        }, {
            headers: {
                'Authorization': `Basic ${token}`,
            }
        }).then((response) => {
        response.data.aggregations.interval.buckets.map((item, i) => {
            graphValues[i] = {
                time: moment.parseZone(item.key_as_string).local().format("dddd"),
                count: item.total_count.value,
            }
        })
    }).catch((error) => {
        console.log(error);
    })
    return graphValues;
}