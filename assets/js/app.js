document.addEventListener("DOMContentLoaded", function () {

    const typeSelect = document.getElementById("type");
    const categorySelect = document.getElementById("category");
    const customInput = document.getElementById("customCategory");

    const categories = {
        income: ["Salary", "Other"],
        expense: ["Food", "Travel", "Bills", "Shopping", "Other"]
    };

    if (typeSelect && categorySelect) {
        typeSelect.addEventListener("change", function () {
            const selectedType = this.value;

            categorySelect.innerHTML = '<option value="">Select category</option>';
            if (customInput) customInput.style.display = "none";

            if (categories[selectedType]) {
                categories[selectedType].forEach(cat => {
                    const option = document.createElement("option");
                    option.value = cat;
                    option.textContent = cat;
                    categorySelect.appendChild(option);
                });

                if (selectedType === "income") {
                    categorySelect.value = "Salary";
                }
            }
        });
    }

    if (categorySelect && customInput) {
        categorySelect.addEventListener("change", function () {
            if (this.value === "Other") {
                customInput.style.display = "block";
                customInput.required = true;
            } else {
                customInput.style.display = "none";
                customInput.required = false;
                customInput.value = "";
            }
        });
    }

    // =========================
    // PIE CHART
    // =========================
    const chartCanvas = document.getElementById("expenseChart");

    if (chartCanvas) {
        fetch('get-chart-data.php')
            .then(res => res.json())
            .then(data => {

                if (!data.length) return;

                const labels = data.map(d => d.category);
                const values = data.map(d => d.total);

                new Chart(chartCanvas, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

            })
            .catch(err => console.error("Chart error:", err));
    }

    // =========================
    // BAR CHART
    // =========================
    const incomeExpenseCanvas = document.getElementById("incomeExpenseChart");

    if (incomeExpenseCanvas) {
        fetch('get-income-expense.php')
            .then(res => res.json())
            .then(data => {

                new Chart(incomeExpenseCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['Income', 'Expense'],
                        datasets: [{
                            label: 'Amount',
                            data: [data.income, data.expense],
                            backgroundColor: ['#22c55e', '#ef4444'],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

            })
            .catch(err => console.error("Chart error:", err));
    }

    const pass = document.getElementById("password");

    if (pass) {
        pass.addEventListener("dblclick", () => {
            pass.type = pass.type === "password" ? "text" : "password";
        });
    }

    const investChart = document.getElementById("investmentChart");

if (investChart) {
    fetch('get-investment-chart.php')
        .then(res => res.json())
        .then(data => {

            const labels = data.map(d => d.name);
            const values = data.map(d => d.current_value);

            new Chart(investChart, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        });
}

});