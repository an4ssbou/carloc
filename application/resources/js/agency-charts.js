import Chart from 'chart.js/auto';

const { revenueThisMonth, revenueLastMonth, joursLabels, dataReservationsJour, fleetOnRental, fleetAvailable } = JSON.parse(document.getElementById('agency-chart-data').textContent);

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: ['Jours 1–7', 'Jours 8–14', 'Jours 15–21', 'Jours 22–28', 'Jours 29–31'],
                datasets: [{
                        label: 'This Month',
                        data: revenueThisMonth,
                        borderColor: '#feb234',
                        backgroundColor: 'rgba(254, 178, 52, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#feb234',
                        pointRadius: 4,
                    },
                    {
                        label: 'Last Month',
                        data: revenueLastMonth,
                        borderColor: '#dfe3e7',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [8, 8],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0,
                    }
                ]
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
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        },
                        ticks: {
                            callback: (v) => v.toLocaleString() + ' DH'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('fleetChart'), {
            type: 'doughnut',
            data: {
                labels: ['On Rental', 'Available'],
                datasets: [{
                    data: [fleetOnRental, fleetAvailable],
                    backgroundColor: ['#feb234', '#6edd83'],
                    borderWidth: 0,
                    cutout: '75%',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        new Chart(document.getElementById('reservationsChart'), {
            type: 'bar',
            data: {
                labels: joursLabels,
                datasets: [{
                    data: dataReservationsJour,
                    backgroundColor: dataReservationsJour.map((v) => {
                        const max = Math.max(...dataReservationsJour);
                        return v === max ?
                            'rgba(13, 33, 55, 0.85)' :
                            'rgba(254, 178, 52, 0.75)';
                    }),
                    borderRadius: 6,
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
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
