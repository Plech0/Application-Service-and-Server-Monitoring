const {PieChart, Pie, Tooltip, ResponsiveContainer, Sector} = Recharts;

const renderActiveShape = (props) => {
    const RADIAN = Math.PI / 180;
    const {
        cx, cy, midAngle, innerRadius, outerRadius, startAngle, endAngle,
        fill, payload, percent, value
    } = props;
    const sin = Math.sin(-RADIAN * midAngle);
    const cos = Math.cos(-RADIAN * midAngle);
    const sx = cx + (outerRadius + 10) * cos;
    const sy = cy + (outerRadius + 10) * sin;
    const mx = cx + (outerRadius + 30) * cos;
    const my = cy + (outerRadius + 30) * sin;
    const ex = mx + (cos >= 0 ? 1 : -1) * 22;
    const ey = my;
    const textAnchor = cos >= 0 ? 'start' : 'end';

    return (
        <g>
            <text x={cx} y={cy} dy={8} textAnchor="middle" fill={fill}>{payload.name}</text>
            <Sector
                cx={cx}
                cy={cy}
                innerRadius={innerRadius}
                outerRadius={outerRadius}
                startAngle={startAngle}
                endAngle={endAngle}
                fill={fill}
            />
            <Sector
                cx={cx}
                cy={cy}
                startAngle={startAngle}
                endAngle={endAngle}
                innerRadius={outerRadius + 6}
                outerRadius={outerRadius + 10}
                fill={fill}
            />
            <path d={`M${sx},${sy}L${mx},${my}L${ex},${ey}`} stroke={fill} fill="none"/>
            <circle cx={ex} cy={ey} r={2} fill={fill} stroke="none"/>
            <text x={ex + (cos >= 0 ? 1 : -1) * 12} y={ey} textAnchor={textAnchor} fill="#333">{`Count ${value}`}</text>
            <text x={ex + (cos >= 0 ? 1 : -1) * 12} y={ey} dy={18} textAnchor={textAnchor} fill="#999">
                {`(Rate ${(percent * 100).toFixed(2)}%)`}
            </text>
        </g>
    );
};

class SimplePie extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            activeIndex: 0,
            data: [],
        };
    }

    componentDidMount() {
        this.refreshGraph();
        setInterval(this.refreshGraph, 300000);
    }

    refreshGraph = async () => {

        let data = await getDataForPie(this.props);
        this.setState({
            data: data,
        })
    }

    onPieEnter = (data, index) => {
        this.setState({
            activeIndex: index,
        })
    }

    render() {
        return (
            <ResponsiveContainer width={"100%"} height={300}>
                <PieChart>
                    <Pie
                        activeIndex={this.state.activeIndex}
                        activeShape={renderActiveShape}
                        data={this.state.data}
                        innerRadius={60}
                        outerRadius={80}
                        fill="#8884d8"
                        onMouseEnter={this.onPieEnter}
                    />
                </PieChart>
            </ResponsiveContainer>
        );
    }

}

function GraphInit(props) {
    return (
        <div>
            <SimplePie
                name={props.name}
                host={props.host}
                es={props.es}
                vhost={props.vhost}
                token={props.token}
            />
        </div>
    );
}

document.querySelectorAll('#graphs .pie-graph').forEach((domContainer) => {
    ReactDOM.render(
        <GraphInit
            name={domContainer.dataset.name}
            host={domContainer.dataset.host}
            es={domContainer.dataset.es}
            vhost={domContainer.dataset.vhost}
            token={domContainer.dataset.token}
        />, domContainer);
})

async function getDataForPie(props) {
    let graphValues = [];
    const token = props.token;
    const url = `${props.es}/filebeat-${props.host}-*/_search`
    let domain = props.vhost.split(".");
    domain = domain[0];
    console.log(domain);
    await axios.post(url,
        {
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
                            exists: {
                                field: "request_type"
                            }
                        },
                        {
                            regexp: {
                                "virtualhost.keyword": {
                                    value: `(${domain}.).*`
                                }
                            }
                        },
                        {
                            term: {
                                "log_type.keyword": {
                                    value: "access_apache"
                                }
                            }
                        },
                        {
                            term: {
                                "request_type.keyword": {
                                    value: "page"
                                }
                            }
                        }
                    ]
                }
            },
            aggs: {
                items: {
                    terms: {
                        field: `${props.name}`,
                        size: 20
                    }
                }
            },
            size: 0,
        }, {
            headers: {
                'Authorization': `Basic ${token}`,
            }
        }).then((response) => {
        response.data.aggregations.items.buckets.map((item, i) => {
            graphValues[i] = {
                name: item.key,
                value: item.doc_count,
            }
        })
    }).catch((error) => {
        console.log(error);
    })
    return graphValues;
}