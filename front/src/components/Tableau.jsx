import React from 'react';

function Tableau({ colonnes, lignes }) {
    const cellStyle = {
        padding: '10px 15px',
        fontSize: '16px'
    };

    const getValue = (obj, path) => {
        return path.split('.').reduce((acc, key) => acc?.[key], obj);
    };

    return (
        <table border="1">
            <thead>
                <tr>
                    {colonnes.map((colonne, index) => (
                        <th key={index} style={cellStyle}>{colonne}</th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {lignes.map((ligne, index) => (
                    <tr key={index}>
                        {colonnes.map((colonne, colIndex) => (
                            <td key={colIndex} style={cellStyle}>
                                {getValue(ligne, colonne)}
                            </td>
                        ))}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

export default Tableau;