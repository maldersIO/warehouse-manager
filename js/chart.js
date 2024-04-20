//jQuery time
jQuery(document).ready(function () {

    // Get the value of the CSS variable
    let primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-primary').trim();
    let secondaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-secondary-color').trim();

    let ctx = document.getElementById("chart").getContext("2d");
    let chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [
                {
                    label: "Incoming goods",
                    backgroundColor: primaryColor, // Use the CSS variable value here for incoming
                    borderColor: "#fff",
                    data: [700, 800, 900, 1000, 1100]
            },
                {
                    label: "Outgoing goods",
                    backgroundColor: secondaryColor, // Use a different CSS variable or color for outgoing
                    borderColor: "#fff",
                    data: [500, 600, 700, 800, 900]
            }
        ]
        },
        options: {
            scales: {
                xAxes: [{
                    stacked: false // Set to true if you want to stack incoming and outgoing bars
            }],
                yAxes: [{
                    stacked: false // Set to true if you want to stack incoming and outgoing bars
            }]
            },
            plugins: {
                title: {
                    display: true,
                    text: "Total goods movement over the last five months"
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
            },
            responsive: true,
            maintainAspectRatio: false // You can adjust this as per your needs
        }
    });
});

jQuery(document).ready(function () {

    // Get the value of the CSS variable
    let primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-primary').trim();
    let secondaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-secondary-color').trim();

    let ctx = document.getElementById("past28days").getContext("2d");

    // Dummy data generation: you can replace these with your actual totals
    let totalIncoming = Array.from({
        length: 28
    }, () => Math.floor(Math.random() * 1000) + 100).reduce((a, b) => a + b, 0);
    let totalOutgoing = Array.from({
        length: 28
    }, () => Math.floor(Math.random() * 1000) + 100).reduce((a, b) => a + b, 0);

    let chart = new Chart(ctx, {
        type: "bar", // Use 'bar' and adjust with indexAxis
        data: {
            labels: ["Incoming", "Outgoing"], // Two labels: Incoming and Outgoing
            datasets: [
                {
                    label: "Goods Movement",
                    backgroundColor: [primaryColor, secondaryColor], // Colors for Incoming and Outgoing
                    data: [totalIncoming, totalOutgoing] // Total values
            }
        ]
        },
        options: {
            indexAxis: 'y', // This makes it horizontal
            scales: {
                x: { // Instead of xAxes
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: "Total goods movement over the last 28 days"
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});


jQuery(document).ready(function () {

    // Get the value of the CSS variable
    let primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-primary').trim();
    let pHoverColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-primary-hover').trim();
    let secondaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-secondary-color').trim();
    let sHoverColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-secondary-hover').trim();
    let warningColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--bs-warning').trim();

    let ctx = document.getElementById("doughnutChart").getContext("2d");
    let doughnutChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["AvailaZn-120", "AvailaCu", "AvailaMn", "MICROPLEX", "AxtraPHY GOLD"],
            datasets: [{
                label: "Quantity in kg",
                backgroundColor: [primaryColor, pHoverColor, secondaryColor, sHoverColor, warningColor],
                borderColor: "#fff",
                data: [300, 400, 500, 200, 600]
        }]
        },
        options: {
            title: {
                text: "Sales Distribution in 2020",
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});