import React, { useState } from 'react';
import { useApi } from '../services/useApi';

function FormHorraire({ onSubmit }) {
    const today = new Date().toISOString().slice(0, 10) + " 00:00:00";
    const [formData, setFormData] = useState({
        utilisateur_id: '',
        entree_sortie: '1', // 1 = entrée, 0 = sortie
        heure: today,
    });

    const { loading, error, callApi } = useApi();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const body = {
                utilisateur_id: formData.utilisateur_id,
                entree_sortie: parseInt(formData.entree_sortie, 10),
                heure: formData.heure,
            };

            const res = await callApi('/heures', {
                method: 'POST',
                body,
            });

            if (onSubmit) {
                // onSubmit with data from API if available, otherwise the body we sent
                onSubmit(res?.data || body);
            }

            setFormData({
                utilisateur_id: '',
                entree_sortie: '1',
                heure: today,
            });
            window.location.reload();
        } catch (err) {
            console.error('Erreur lors de la création de l\'heure :', err);
        }
    };

    return (
        <div style={{ padding: '1.5rem' }}>
            <h1 style={{ fontSize: '1.5rem', fontWeight: 'bold', marginBottom: '1rem' }}>
                Ajouter une heure
            </h1>

            <form
                onSubmit={handleSubmit}
                style={{
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '1rem',
                    maxWidth: '400px',
                }}
            >
                <label style={{ display: 'flex', flexDirection: 'column' }}>
                    ID utilisateur :
                    <input
                        type="number"
                        name="utilisateur_id"
                        value={formData.utilisateur_id}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label style={{ display: 'flex', flexDirection: 'column' }}>
                    Type :
                    <select
                        name="entree_sortie"
                        value={formData.entree_sortie}
                        onChange={handleChange}
                        required
                    >
                        <option value="1">Entrée</option>
                        <option value="0">Sortie</option>
                    </select>
                </label>

                <label style={{ display: 'flex', flexDirection: 'column' }}>
                    Heure :
                    <input
                        type="text"
                        name="heure"
                        value={formData.heure}
                        onChange={handleChange}
                        placeholder="YYYY-MM-DD HH:MM:SS"
                        required
                    />
                </label>

                <button type="submit" disabled={loading}>
                    {loading ? 'Envoi...' : 'Soumettre'}
                </button>

                {error && (
                    <p style={{ color: 'red', margin: 0 }}>Erreur lors de l'envoi</p>
                )}
            </form>
        </div>
    );
}

export default FormHorraire;