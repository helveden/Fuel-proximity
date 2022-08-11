import React, {Component} from 'react';

import axios from 'axios';

class Default extends Component {

    
    constructor(props) {
        super(props);
        this.state = {
            city: "",
            results: [],
            openPdv: false,
            pdvs: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);

        ///
        this.processOpenPdv = this.processOpenPdv.bind(this);
    }
    
    
    componentDidMount() {
        // rechercher s'il y a un id pour l'update
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(this.props.datas !== prevProps.datas) {
            this.setState({
                datas: this.props.datas
            });
        }
    }
    
    handleChange(event) {   
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
    
        this.setState({
            [name]: value
        });
    }

    async handleSubmit(event) {
        event.preventDefault();
        var results = await axios.post('/get-cities', this.state)
        .then(function(response) {
            return response;
        });

        console.log(results)

        
        this.setState({
            results: results.data.features
        });
    }

    async processOpenPdv(city) {
        
        var results = await axios.post('/get-pdvs', {context: city.context})
        .then(function(response) {
            return response;
        });

        console.log(results)

        this.setState({
            openPdv: true,
            pdvs: results.data
        });
    }

    render() {

        const results = this.state.results
        const openPdv = this.state.openPdv
        const pdvs = this.state.pdvs

        return (
            <>
                <form
                    onSubmit={this.handleSubmit}
                >
                    <input
                        type="text" 
                        className="form-control" 
                        placeholder="Ville"
                        name="city"
                        value={this.state.city}
                        onChange={this.handleChange}
                    />
                    
                    <div className="form-group">
                        <button type="submit" className='btn btn-info'>Ok</button>
                    </div>
                </form>
                <table>
                    <tbody>
                        {results.map((feature, index) => {
                            return <tr key={index}>
                                <td><button type="button" onClick={() => this.processOpenPdv(feature.properties)}>{feature.properties.city}</button></td>
                                <td>{feature.properties.postcode}</td>
                                <td>{feature.properties.context}</td>
                            </tr>
                        })}
                    </tbody>
                </table>
                { openPdv ? 
                    <section>
                        <h6>Pdvs</h6>
                        <table>
                            <tbody>
                                {pdvs.map((pdv, index) => {
                                    return <tr key={index}>
                                        <td>{pdv.datas.ville}</td>
                                    </tr>
                                })}
                            </tbody>
                        </table>
                    </section>
                : null
                }
            </>  
        ) 
    }
}
export default Default;