import React, { useState } from 'react';

function FormHorraire({ onSubmit }) {
    const [formData, setFormData] = useState({
        nom: '',
        prenom: '',
        choix: '',
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value,
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const currentTime = new Date().toLocaleTimeString();
        onSubmit({ ...formData, heureRemplissage: currentTime });
        setFormData({ nom: '', prenom: '', choix: '' });
    };

    return (
        <form onSubmit={handleSubmit}>
            <label>
                Nom:
                <input
                    type="text"
                    name="nom"
                    value={formData.nom}
                    onChange={handleChange}
                    required
                />
            </label>
            <br />
            <label>
                Prénom:
                <input
                    type="text"
                    name="prenom"
                    value={formData.prenom}
                    onChange={handleChange}
                    required
                />
            </label>
            <br />
            <label>
                Choix:
                <select
                    name="choix"
                    value={formData.choix}
                    onChange={handleChange}
                    required
                >
                    <option value="">--Sélectionnez--</option>
                    <option value="Entrée matin">Entrée matin</option>
                    <option value="Sortie midi">Sortie midi</option>
                    <option value="Entrée midi">Entrée midi</option>
                    <option value="Sortie soir">Sortie soir</option>
                </select>
            </label>
            <br />
            <button type="submit">Soumettre</button>
        </form>
    );
}

export default FormHorraire;