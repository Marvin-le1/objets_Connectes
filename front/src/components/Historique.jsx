import React, { useEffect, useState } from "react";
import { useApi } from "../services/useApi";
import Tableau from "./Tableau";

function Historique() {
    const { callApi, loading, error } = useApi();
    const [users, setUsers] = useState([]);
    const [selectedId, setSelectedId] = useState("");
    const [historique, setHistorique] = useState(null);

    // Charger les utilisateurs
    useEffect(() => {
        callApi("/utilisateurs/")
            .then((res) => {
                const list = res?.data ?? [];
                setUsers(list);
                if (list.length > 0) setSelectedId(list[0].id);
            })
            .catch((err) => console.error(err));
    }, [callApi]);

    // Charger l'historique dès qu'un utilisateur change
    useEffect(() => {
        if (!selectedId) return;
        callApi(`/badgeage/historique/${selectedId}`)
            .then((res) => setHistorique(res?.data ?? null))
            .catch((err) => console.error(err));
    }, [callApi, selectedId]);

    const colonnes = ["id", "type", "heure", "date"];
    const lignes = historique?.badgeages ?? [];
    const utilisateur = historique?.utilisateur;

    return (
        <div style={{ padding: "1.5rem" }}>
            <h1 style={{ fontSize: "1.5rem", fontWeight: "bold", marginBottom: "1rem" }}>
                Historique des badgeages
            </h1>

            {/* Sélection utilisateur */}
            <div
                style={{
                    marginBottom: "1rem",
                    display: "flex",
                    gap: "1rem",
                    alignItems: "center",
                    flexWrap: "wrap",
                }}
            >
                <label>
                    Utilisateur :
                    <select
                        value={selectedId}
                        onChange={(e) => setSelectedId(e.target.value)}
                        style={{ marginLeft: "0.5rem" }}
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
                    <p style={{ margin: 0, color: "red" }}>
                        {error?.message || "Erreur lors du chargement"}
                    </p>
                )}
            </div>

            {/* Infos utilisateur */}
            {utilisateur && (
                <div style={{ marginBottom: "1rem" }}>
                    <p>
                        <strong>Utilisateur :</strong> {utilisateur.prenom} {utilisateur.nom}
                    </p>
                </div>
            )}

            {/* Tableau des badgeages */}
            <Tableau colonnes={colonnes} lignes={lignes} />
        </div>
    );
}

export default Historique;