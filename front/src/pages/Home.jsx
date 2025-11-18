import * as React from 'react';
import { useState, useEffect } from 'react';
import Tableau from '../components/Tableau';
import FormHorraire from '../components/ForrmHorraire';
import { useApi } from '../services/useApi';
import Status from '../components/Status';

function Home() {
    const [lignes, setLignes] = useState([]);
    const { data, loading, error, callApi } = useApi();

    useEffect(() => {
        callApi("/utilisateurs/")
          .then((res) => {
            setLignes(res?.data ?? []);
          })
          .catch((err) => {
            console.error(err);
          });
      }, [callApi]);

    return (
        <div>
            <Tableau
            colonnes={['nom', 'prenom', 'service', 'badge.numero']}
            lignes={lignes}
            />

            <FormHorraire />

            <Status />
        </div>
    );
}

export default Home;