import React, { useEffect, useState } from 'react';
import { useApi } from '../services/useApi';
import Tableau from './Tableau';

function Status() {
    const { callApi, loading, error } = useApi();
    const [users, setUsers] = useState([]);
    const [selectedId, setSelectedId] = useState('');
    const [statusRow, setStatusRow] = useState(null);

    useEffect(() => {
        callApi('/utilisateurs/')
            .then((res) => {
                const list = res?.data ?? [];
                setUsers(list);
                if (list.length > 0) {
                    setSelectedId(list[0].id); 
                }
            })
            .catch((err) => console.error(err));
    }, [callApi]);

    useEffect(() => {
        if (!selectedId) return;

        callApi(`/badgeage/statut/${selectedId}`)
            .then((res) => {
                const d = res?.data;
                if (!d) return;
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
        <div style={{ padding: '1.5rem' }}>
            <h1 style={{ fontSize: '1.5rem', fontWeight: 'bold', marginBottom: '1rem' }}>
                Statut des utilisateurs
            </h1>

            <div
                style={{
                    marginBottom: '1rem',
                    display: 'flex',
                    gap: '1rem',
                    alignItems: 'center',
                    flexWrap: 'wrap',
                }}
            >
                <label>
                    Utilisateur :{' '}
                    <select
                        value={selectedId}
                        onChange={(e) => setSelectedId(e.target.value)}
                    >
                        {users.map((u) => (
                            <option key={u.id} value={u.id}>
                                {u.prenom} {u.nom}
                            </option>
                        ))}
                    </select>
                </label>

                {loading && <p style={{ margin: 0 }}>Chargement...</p>}

                {error && (
                    <p style={{ margin: 0, color: 'red' }}>
                        Erreur : {error?.message || 'Une erreur est survenue.'}
                    </p>
                )}
            </div>

            <Tableau colonnes={colonnes} lignes={lignes} />
        </div>
    );
}

export default Status;