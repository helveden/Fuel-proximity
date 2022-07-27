import React, {Component} from 'react';

import axios from 'axios';

class Default extends Component {

    
    constructor(props) {
        super(props);
        this.state = {
            city: "",
            results: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
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
        var results = await axios.post('/get-pdv', this.state)
        .then(function(response) {
            return response;
        });

        console.log(results)

        
        this.setState({
            results: results.data.features
        });
    }

    render() {

        const results = this.state.results
        
        console.log(results)
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
                                <td>{feature.properties.label}</td>
                            </tr>
                        })}
                    </tbody>
                </table>
            </>  
        ) 
    }
}
export default Default;