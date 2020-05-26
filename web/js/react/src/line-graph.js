const {LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer} = Recharts;

class LineCharts extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: [],
            timeNumber: 10,
            timeUnit: "m",
        }
    }

    componentDidMount() {
        this.refreshGraph();
        setInterval(this.refreshGraph, 15000);
    }

    refreshGraph = async () => {
        let data = await getDataForPct(this.props, this.state.timeNumber, this.state.timeUnit);
        this.setState({
            data: data,
        })
    }

    updateTimeUnit = (event) => {
        this.setState({
            timeUnit: event.target.value,
        })
        this.refreshGraph();
    }

    updateTimeNumber = (event) => {
        this.setState({
            timeNumber: parseInt(event.target.value, 10)
        })
        this.refreshGraph();
    }

    render() {
        return (
            <div className={'graph-wrapper'}>
                <div className={'graph-settings'}>
                    <input type="number" value={this.state.timeNumber} onChange={this.updateTimeNumber}/>
                    <select name="units" onChange={this.updateTimeUnit} value={this.state.timeUnit}>
                        <option value="d">Days ago</option>
                        <option value="h">Hours ago</option>
                        <option value="m" selected>Minutes ago</option>
                    </select>
                </div>
                <ResponsiveContainer width={"100%"} height={300}>
                    <LineChart data={this.state.data}
                               margin={{top: 5, right: 30, left: 20, bottom: 5}}>
                        <XAxis dataKey="time" reversed={true}/>
                        <YAxis domain={[0, 100]}/>
                        <CartesianGrid strokeDasharray="3 3"/>
                        <Tooltip/>
                        <Legend/>
                        <Line type="monotone" dataKey="value" stroke="#8884d8"
                              name={`${this.props.monitoringUnit} usage`}
                              dot={false}/>
                    </LineChart>
                </ResponsiveContainer>
            </div>
        );
    }
}

function GraphInit(props) {
    return (
        <div>
            <LineCharts
                fieldName={props.fieldName}
                monitoringUnit={props.monitoringUnit}
                type={props.type}
                host={props.host}
                es={props.es}
                token={props.token}
            />
        </div>);
}

document.querySelectorAll('#graphs .line-graph').forEach((domContainer) => {
    ReactDOM.render(
        <GraphInit
            type={domContainer.dataset.type}
            fieldName={domContainer.dataset.fieldname}
            monitoringUnit={domContainer.dataset.monitoringunit}
            host={domContainer.dataset.host}
            es={domContainer.dataset.es}
            token={domContainer.dataset.token}
        />, domContainer);
})

async function getDataForPct(props, time, unit) {
    let graphValues = [];
    const searchTimeStart = moment().format();
    const searchTimeEnd = moment().subtract(time, unit).format();
    const token = props.token;
    const url = `${props.es}/metricbeat-${props.host}-*/_search`;
    await axios.post(url,
        {
            query: {
                bool: {
                    filter: [
                        {
                            exists: {
                                field: `${props.fieldName}`
                            },
                        },
                        {
                            range: {
                                "@timestamp": {
                                    lte: `${searchTimeStart}`,
                                    gte: `${searchTimeEnd}`
                                }
                            },
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
            let time = new Date(hit._source['@timestamp']);
            if (props.monitoringUnit === 'CPU') {
                graphValues[i] = {
                    time: time.toLocaleTimeString(),
                    value: props.type === 'pct' ? Math.floor(hit._source.system.cpu.total.norm.pct * 100) : hit._source.system.cpu.total.norm.pct
                }
            } else if (props.monitoringUnit === 'Memory') {
                graphValues[i] = {
                    time: time.toLocaleTimeString(),
                    value: props.type === 'pct' ? Math.floor(hit._source.system.memory.actual.used.pct * 100) : hit._source.system.memory.actual.used.pct
                }
            }
        });
    }).catch((error) => {
        console.log(error);
    })
    let result = [];
    let j = 0;
    let avg = 0;
    let i = 0;
    const chunk = Math.ceil(graphValues.length / 200)
    for (i; i < graphValues.length; i++) {
        avg = avg + graphValues[i].value;
        j++;
        if (j >= chunk) {
            let midPosition = ((i + 1) - Math.floor(chunk / 2));
            midPosition = midPosition < 1 ? 1 : midPosition;
            result.push({
                value: (avg / chunk).toFixed(2),
                time: graphValues[midPosition - 1].time
            })
            j = 0;
            avg = 0;
        }
    }
    if (avg !== 0) {
        result.push({
            value: (avg / chunk).toFixed(2),
            time: graphValues[i - 1].time
        })
    }
    return result;
}