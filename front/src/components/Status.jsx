import React, { useEffect, useState } from 'react';
import { useApi } from '../services/useApi';
import Tableau from './Tableau';

function Status() {
    const { callApi, loading, error } = useApi();

    const [users, setUsers] = useState([]);
    const [selectedId, setSelectedId] = useState('');
    const [statusRow, setStatusRow] = useState(null);

    // 1) Récupérer tous les utilisateurs pour le select
    useEffect(() => {
        callApi('/utilisateurs/')
            .then((res) => {
                const list = res?.data ?? [];
                setUsers(list);
                if (list.length > 0) {
                    setSelectedId(list[0].id); // user par défaut
                }
            })
            .catch((err) => console.error(err));
    }, [callApi]);

    // 2) Récupérer le statut de l'utilisateur sélectionné
    useEffect(() => {
        if (!selectedId) return;

        callApi(`/badgeage/statut/${selectedId}`)
            .then((res) => {
                const d = res?.data;
                if (!d) return;

                // On “aplatit” l’objet pour le tableau
                setStatusRow({
                    nom: d.utilisateur?.nom,
                    prenom: d.utilisateur?.prenom,
                    au_travail: d.au_travail ? 'Au travail' : 'Absent',
                    dernier_badgeage: d.dernier_badgeage
                        ? `${d.dernier_badgeage.type} - ${d.dernier_badgeage.heure}`
                        : '-',
                    heures_travaillees_ojd: d.heures_travaillees_ojd ?? '0h00',
                });
            })
            .catch((err) => console.error(err));
    }, [selectedId, callApi]);

    const colonnes = [
        'nom',
        'prenom',
        'au_travail',
        'dernier_badgeage',
        'heures_travaillees_ojd',
    ];

    const lignes = statusRow ? [statusRow] : [];

    return (
        <div>
            <label>
                Utilisateur :{' '}
                <select
                    value={selectedId}
                    onChange={(e) => setSelectedId(e.target.value)}
                >
                    {users.map((u) => (
                        <option key={u.id} value={u.id}>
                            {u.nom} {u.prenom}
                        </option>
                    ))}
                </select>
            </label>

            {loading && <p>Chargement...</p>}
            {error && <p>Erreur : {error.message}</p>}

            <Tableau colonnes={colonnes} lignes={lignes} />
        </div>
    );
}

export default Status;