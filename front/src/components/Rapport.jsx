import React, { useEffect, useState } from 'react';
import { useApi } from '../services/useApi';
import Tableau from './Tableau';

const Rapport = () => {
    const { callApi, loading, error } = useApi();

    const [users, setUsers] = useState([]);
    const [selectedUserId, setSelectedUserId] = useState('');
    const [dateDebut, setDateDebut] = useState('');
    const [dateFin, setDateFin] = useState('');
    const [rapport, setRapport] = useState(null);

    // 🔹 Récup liste des utilisateurs au chargement
    useEffect(() => {
        callApi('/utilisateurs/')
            .then((res) => {
                const list = res?.data ?? [];
                setUsers(list);
                if (list.length > 0) {
                    setSelectedUserId(list[0].id);
                }
            })
            .catch((err) => console.error(err));
    }, [callApi]);

    // 🔹 Soumission du formulaire => POST rapport
    const handleSubmit = (e) => {
        e.preventDefault();

        if (!selectedUserId || !dateDebut || !dateFin) return;

        callApi('/badgeage/rapport', {
            method: 'POST',
            body: {
                utilisateur_id: selectedUserId,
                date_debut: dateDebut, // format "01/01/2000"
                date_fin: dateFin,
            },
        })
            .then((res) => {
                const data = res?.data ?? null;
                setRapport(data);
            })
            .catch((err) => console.error(err));
    };

    if (loading && !rapport) {
        return <p>Chargement du rapport...</p>;
    }

    if (error) {
        const message =
            typeof error === 'string'
                ? error
                : error?.message || "Une erreur est survenue lors du chargement du rapport.";
        return <p style={{ color: 'red' }}>{message}</p>;
    }

    const utilisateur = rapport?.utilisateur;
    const periode = rapport?.periode;
    const details_par_jour = rapport?.details_par_jour ?? [];
    const total_heures_travaillees = rapport?.total_heures_travaillees;

    return (
        <div style={{ padding: '1.5rem' }}>
            <h1 style={{ fontSize: '1.5rem', fontWeight: 'bold', marginBottom: '1rem' }}>
                Rapport de badgeage
            </h1>

            {/* 🔹 Formulaire de sélection */}
            <form
                onSubmit={handleSubmit}
                style={{
                    marginBottom: '1.5rem',
                    display: 'flex',
                    gap: '1rem',
                    flexWrap: 'wrap',
                    alignItems: 'center',
                }}
            >
                <label>
                    Utilisateur :{' '}
                    <select
                        value={selectedUserId}
                        onChange={(e) => setSelectedUserId(e.target.value)}
                    >
                        {users.map((u) => (
                            <option key={u.id} value={u.id}>
                                {u.prenom} {u.nom}
                            </option>
                        ))}
                    </select>
                </label>

                <label>
                    Date début :{' '}
                    <input
                        type="text"
                        placeholder="01/01/2000"
                        value={dateDebut}
                        onChange={(e) => setDateDebut(e.target.value)}
                    />
                </label>

                <label>
                    Date fin :{' '}
                    <input
                        type="text"
                        placeholder="01/01/2030"
                        value={dateFin}
                        onChange={(e) => setDateFin(e.target.value)}
                    />
                </label>

                <button type="submit" disabled={loading}>
                    Générer le rapport
                </button>
            </form>

            {/* 🔹 Affichage du rapport seulement si dispo */}
            {rapport && (
                <>
                    <div style={{ marginBottom: '1rem' }}>
                        <p>
                            <strong>Utilisateur :</strong> {utilisateur?.prenom} {utilisateur?.nom}
                        </p>
                        <p>
                            <strong>Période :</strong> {periode?.debut} → {periode?.fin}
                        </p>
                        <p>
                            <strong>Total d'heures travaillées :</strong> {total_heures_travaillees}
                        </p>
                    </div>

                    <Tableau
                        colonnes={['date', 'heures_travaillees', 'nombre_badgeages']}
                        lignes={details_par_jour}
                    />
                </>
            )}
        </div>
    );
};

export default Rapport;