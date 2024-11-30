document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("student-table-body");

    fetch("fetch_data.php")
        .then(response => response.json())
        .then(data => {
            data.forEach((student, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${student.cne}</td>
                    <td>${student.cni}</td>
                    <td>${student.nom}</td>
                    <td>${student.prenom}</td>
                    <td>${student.email}</td>
                    <td>${student.date_naissance}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
});
