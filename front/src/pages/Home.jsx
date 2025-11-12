import * as React from 'react';
import { useState } from 'react';
import Tableau from '../components/Tableau';
import FormHorraire from '../components/ForrmHorraire';

function Home() {
    const [lignes, setLignes] = useState([]);

    const handleFormSubmit = (formData) => {
        setLignes([...lignes, formData]);
    };

    return (
        <div>
            <Tableau
                colonnes={['nom', 'prenom', 'choix', 'heureRemplissage']}
                lignes={lignes}
            />

            <FormHorraire onSubmit={handleFormSubmit} />
            {console.log(lignes)}
        </div>
    );
}

export default Home;