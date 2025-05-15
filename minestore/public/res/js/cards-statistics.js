/**
 * Statistics Cards
 */

"use strict";


let cardColor, shadeColor, labelColor, headingColor, borderColor, legendColor;

if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
    legendColor = config.colors_dark.bodyColor;
    shadeColor = "dark";
} else {
    cardColor = config.colors.cardColor;
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
    legendColor = config.colors.bodyColor;
    shadeColor = "";
}

// Donut Chart Colors
const chartColors = {
    donut: {
        series1: config.colors.success,
        series2: "#99E570",
        series3: "#B5EC97",
        series4: "#E3F8D7"
    }
};

// Order Area Chart
// --------------------------------------------------------------------
const orderAreaChartEl = document.querySelector("#orderChart"),
    orderAreaChartConfig = {
        chart: {
            height: 80,
            type: "area",
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: true
            }
        },
        markers: {
            size: 6,
            colors: "transparent",
            strokeColors: "transparent",
            strokeWidth: 4,
            discrete: [
                {
                    fillColor: cardColor,
                    seriesIndex: 0,
                    dataPointIndex: 6,
                    strokeColor: config.colors.success,
                    strokeWidth: 2,
                    size: 6,
                    radius: 8
                }
            ],
            hover: {
                size: 7
            }
        },
        grid: {
            show: false,
            padding: {
                right: 8
            }
        },
        colors: [config.colors.success],
        fill: {
            type: "gradient",
            gradient: {
                shade: shadeColor,
                shadeIntensity: 0.8,
                opacityFrom: 0.8,
                opacityTo: 0.25,
                stops: [0, 85, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 2,
            curve: "smooth"
        },
        series: [
            {
                data: [180, 175, 275, 140, 205, 190, 295]
            }
        ],
        xaxis: {
            show: false,
            lines: {
                show: false
            },
            labels: {
                show: false
            },
            stroke: {
                width: 0
            },
            axisBorder: {
                show: false
            }
        },
        yaxis: {
            stroke: {
                width: 0
            },
            show: false
        }
    };
if (typeof orderAreaChartEl !== undefined && orderAreaChartEl !== null) {
    const orderAreaChart = new ApexCharts(orderAreaChartEl, orderAreaChartConfig);
    orderAreaChart.render();
}

// Revenue Bar Chart
// --------------------------------------------------------------------
function buildWeeklyVisitsChart(categories, data, day) {
    const revenueBarChartEl = document.querySelector("#revenueChart"),
        revenueBarChartConfig = {
            chart: {
                height: 80,
                type: "bar",
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    barHeight: "80%",
                    columnWidth: "45%",
                    startingShape: "rounded",
                    endingShape: "rounded",
                    borderRadius: 2,
                    distributed: true
                }
            },
            grid: {
                show: false,
                padding: {
                    top: -20,
                    bottom: -12,
                    left: -10,
                    right: 0
                }
            },
            colors: [
                (day === "Mon") ? config.colors.primary : config.colors_label.primary,
                (day === "Tue") ? config.colors.primary : config.colors_label.primary,
                (day === "Wed") ? config.colors.primary : config.colors_label.primary,
                (day === "Thu") ? config.colors.primary : config.colors_label.primary,
                (day === "Fri") ? config.colors.primary : config.colors_label.primary,
                (day === "Sat") ? config.colors.primary : config.colors_label.primary,
                (day === "Sun") ? config.colors.primary : config.colors_label.primary
            ],
            dataLabels: {
                enabled: false
            },
            series: [
                {
                    data: data
                }
            ],
            legend: {
                show: false
            },
            xaxis: {
                categories: categories,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px"
                    }
                }
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            tooltip: {
                y: {
                    title: {
                        formatter: function(seriesName) {
                            return "";
                        }
                    }
                }
            }
        };
    if (typeof revenueBarChartEl !== undefined && revenueBarChartEl !== null) {
        const revenueBarChart = new ApexCharts(revenueBarChartEl, revenueBarChartConfig);
        revenueBarChart.render();
    }
}

// Total Revenue Report Chart - Bar Chart
// --------------------------------------------------------------------
function buildTotalRevenueChart(current, previous) {
    const totalRevenueChartEl = document.querySelector("#totalRevenueChart"),
        totalRevenueChartOptions = {
            series: [
                {
                    name: current.name,
                    data: current.data
                },
                {
                    name: previous.name,
                    data: previous.data
                }
            ],
            chart: {
                height: 300,
                stacked: true,
                type: "bar",
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "30%",
                    borderRadius: 8,
                    startingShape: "rounded",
                    endingShape: "rounded"
                }
            },
            colors: [config.colors.primary, config.colors.info],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: "smooth",
                width: 2,
                lineCap: "round",
                colors: [cardColor]
            },
            legend: {
                show: true,
                horizontalAlign: "left",
                position: "top",
                markers: {
                    height: 8,
                    width: 8,
                    radius: 12,
                    offsetX: -3
                },
                labels: {
                    colors: legendColor
                },
                itemMargin: {
                    horizontal: 10
                }
            },
            grid: {
                borderColor: borderColor,
                padding: {
                    top: 0,
                    bottom: -8,
                    left: 20,
                    right: 20
                }
            },
            xaxis: {
                categories: current.categories,
                labels: {
                    style: {
                        fontSize: "13px",
                        colors: labelColor
                    }
                },
                axisTicks: {
                    show: false
                },
                axisBorder: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: "13px",
                        colors: labelColor
                    }
                }
            },
            responsive: [
                {
                    breakpoint: 1700,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 10,
                                columnWidth: "35%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1440,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 12,
                                columnWidth: "43%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1300,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 11,
                                columnWidth: "45%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1200,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 11,
                                columnWidth: "37%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 1040,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 12,
                                columnWidth: "45%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 991,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 12,
                                columnWidth: "33%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 768,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 11,
                                columnWidth: "28%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 640,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 11,
                                columnWidth: "30%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 576,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 10,
                                columnWidth: "38%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 440,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 10,
                                columnWidth: "50%"
                            }
                        }
                    }
                },
                {
                    breakpoint: 380,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 9,
                                columnWidth: "60%"
                            }
                        }
                    }
                }
            ],
            states: {
                hover: {
                    filter: {
                        type: "none"
                    }
                },
                active: {
                    filter: {
                        type: "none"
                    }
                }
            }
        };
    if (typeof totalRevenueChartEl !== undefined && totalRevenueChartEl !== null) {
        const totalRevenueChart = new ApexCharts(totalRevenueChartEl, totalRevenueChartOptions);
        totalRevenueChart.render();

        return totalRevenueChart;
    }
}

// Growth Chart - Radial Bar Chart
// --------------------------------------------------------------------
function buildGrowthChart(percentage) {
    const growthChartEl = document.querySelector("#growthChart"),
        growthChartOptions = {
            series: [percentage],
            labels: ["Growth"],
            chart: {
                height: 240,
                type: "radialBar"
            },
            plotOptions: {
                radialBar: {
                    size: 150,
                    offsetY: 10,
                    startAngle: -150,
                    endAngle: 150,
                    hollow: {
                        size: "55%"
                    },
                    track: {
                        background: cardColor,
                        strokeWidth: "100%"
                    },
                    dataLabels: {
                        name: {
                            offsetY: 15,
                            color: legendColor,
                            fontSize: "15px",
                            fontWeight: "600",
                            fontFamily: "Public Sans"
                        },
                        value: {
                            offsetY: -25,
                            color: headingColor,
                            fontSize: "22px",
                            fontWeight: "500",
                            fontFamily: "Public Sans"
                        }
                    }
                }
            },
            colors: [config.colors.primary],
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    shadeIntensity: 0.5,
                    gradientToColors: [config.colors.primary],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 0.6,
                    stops: [30, 70, 100]
                }
            },
            stroke: {
                dashArray: 5
            },
            grid: {
                padding: {
                    top: -35,
                    bottom: -10
                }
            },
            states: {
                hover: {
                    filter: {
                        type: "none"
                    }
                },
                active: {
                    filter: {
                        type: "none"
                    }
                }
            }
        };
    if (typeof growthChartEl !== undefined && growthChartEl !== null) {
        const growthChart = new ApexCharts(growthChartEl, growthChartOptions);
        growthChart.render();

        return growthChart;
    }
}

// Profit Bar Chart
// --------------------------------------------------------------------
const profitBarChartEl = document.querySelector("#profitChart"),
    profitBarChartConfig = {
        series: [
            {
                data: [58, 28, 50, 80]
            },
            {
                data: [50, 22, 65, 72]
            }
        ],
        chart: {
            type: "bar",
            height: 90,
            toolbar: {
                tools: {
                    download: false
                }
            }
        },
        plotOptions: {
            bar: {
                columnWidth: "65%",
                startingShape: "rounded",
                endingShape: "rounded",
                borderRadius: 3,
                dataLabels: {
                    show: false
                }
            }
        },
        grid: {
            show: false,
            padding: {
                top: -30,
                bottom: -12,
                left: -10,
                right: 0
            }
        },
        colors: [config.colors.success, config.colors_label.success],
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 5,
            colors: cardColor
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: ["Jan", "Apr", "Jul", "Oct"],
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: labelColor,
                    fontSize: "13px"
                }
            }
        },
        yaxis: {
            labels: {
                show: false
            }
        }
    };
if (typeof profitBarChartEl !== undefined && profitBarChartEl !== null) {
    const profitBarChart = new ApexCharts(profitBarChartEl, profitBarChartConfig);
    profitBarChart.render();
}

// Session Area Chart
// --------------------------------------------------------------------
const sessionAreaChartEl = document.querySelector("#sessionsChart"),
    sessionAreaChartConfig = {
        chart: {
            height: 90,
            type: "area",
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: true
            }
        },
        markers: {
            size: 6,
            colors: "transparent",
            strokeColors: "transparent",
            strokeWidth: 4,
            discrete: [
                {
                    fillColor: cardColor,
                    seriesIndex: 0,
                    dataPointIndex: 8,
                    strokeColor: config.colors.warning,
                    strokeWidth: 2,
                    size: 6,
                    radius: 8
                }
            ],
            hover: {
                size: 7
            }
        },
        grid: {
            show: false,
            padding: {
                right: 8
            }
        },
        colors: [config.colors.warning],
        fill: {
            type: "gradient",
            gradient: {
                shade: shadeColor,
                shadeIntensity: 0.8,
                opacityFrom: 0.8,
                opacityTo: 0.25,
                stops: [0, 95, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 2,
            curve: "straight"
        },
        series: [
            {
                data: [280, 280, 240, 240, 200, 200, 260, 260, 310]
            }
        ],
        xaxis: {
            show: false,
            lines: {
                show: false
            },
            labels: {
                show: false
            },
            axisBorder: {
                show: false
            }
        },
        yaxis: {
            show: false
        }
    };
if (typeof sessionAreaChartEl !== undefined && sessionAreaChartEl !== null) {
    const sessionAreaChart = new ApexCharts(sessionAreaChartEl, sessionAreaChartConfig);
    sessionAreaChart.render();
}

// Total Sales Radial Bar Chart
// --------------------------------------------------------------------
const expensesRadialChartEl = document.querySelector("#expensesChart"),
    expensesRadialChartConfig = {
        chart: {
            sparkline: {
                enabled: true
            },
            parentHeightOffset: 0,
            type: "radialBar"
        },
        colors: [config.colors.primary],
        series: [78],
        plotOptions: {
            radialBar: {
                startAngle: -90,
                endAngle: 90,
                hollow: {
                    size: "65%"
                },
                track: {
                    background: config.colors_label.secondary
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        fontSize: "22px",
                        color: headingColor,
                        fontWeight: 500,
                        offsetY: 0
                    }
                }
            }
        },
        grid: {
            show: false,
            padding: {
                left: -10,
                right: -10
            }
        },
        stroke: {
            lineCap: "round"
        },
        labels: ["Progress"]
    };
if (typeof expensesRadialChartEl !== undefined && expensesRadialChartEl !== null) {
    const expensesRadialChart = new ApexCharts(expensesRadialChartEl, expensesRadialChartConfig);
    expensesRadialChart.render();
}

// Visitor Bar Chart
// --------------------------------------------------------------------
// const visitorBarChartEl = document.querySelector("#visitorsChart"),
//         visitorBarChartConfig = {
//             chart: {
//                 height: 120,
//                 width: 200,
//                 parentHeightOffset: 0,
//                 type: "bar",
//                 toolbar: {
//                     show: false
//                 }
//             },
//             plotOptions: {
//                 bar: {
//                     barHeight: "75%",
//                     columnWidth: "40px",
//                     startingShape: "rounded",
//                     endingShape: "rounded",
//                     borderRadius: 5,
//                     distributed: true
//                 }
//             },
//             grid: {
//                 show: false,
//                 padding: {
//                     top: -25,
//                     bottom: -12
//                 }
//             },
//             colors: [
//                 config.colors_label.primary,
//                 config.colors_label.primary,
//                 config.colors_label.primary,
//                 config.colors_label.primary,
//                 config.colors_label.primary,
//                 config.colors.primary,
//                 config.colors_label.primary
//             ],
//             dataLabels: {
//                 enabled: false
//             },
//             series: [
//                 {
//                     data: [40, 95, 60, 45, 90, 50, 75]
//                 }
//             ],
//             legend: {
//                 show: false
//             },
//             xaxis: {
//                 categories: ["M", "T", "W", "T", "F", "S", "S"],
//                 axisBorder: {
//                     show: false
//                 },
//                 axisTicks: {
//                     show: false
//                 },
//                 labels: {
//                     style: {
//                         colors: labelColor,
//                         fontSize: "13px"
//                     }
//                 }
//             },
//             yaxis: {
//                 labels: {
//                     show: false
//                 }
//             },
//             responsive: [
//                 {
//                     breakpoint: 1440,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 9,
//                                 columnWidth: "60%"
//                             }
//                         }
//                     }
//                 },
//                 {
//                     breakpoint: 1300,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 9,
//                                 columnWidth: "60%"
//                             }
//                         }
//                     }
//                 },
//                 {
//                     breakpoint: 1200,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 8,
//                                 columnWidth: "50%"
//                             }
//                         }
//                     }
//                 },
//                 {
//                     breakpoint: 1040,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 8,
//                                 columnWidth: "50%"
//                             }
//                         }
//                     }
//                 },
//                 {
//                     breakpoint: 991,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 8,
//                                 columnWidth: "50%"
//                             }
//                         }
//                     }
//                 },
//                 {
//                     breakpoint: 420,
//                     options: {
//                         plotOptions: {
//                             bar: {
//                                 borderRadius: 8,
//                                 columnWidth: "50%"
//                             }
//                         }
//                     }
//                 }
//             ]
//         };
//     if (typeof visitorBarChartEl !== undefined && visitorBarChartEl !== null) {
//         const visitorBarChart = new ApexCharts(visitorBarChartEl, visitorBarChartConfig);
//         visitorBarChart.render();
//     }

// Order Statistics Chart
// --------------------------------------------------------------------
function buildPopularPackagesChart(categories, data) {
    const chartOrderStatistics = document.querySelector("#orderStatisticsChart"),
        orderChartConfig = {
            chart: {
                height: 165,
                width: 130,
                type: "donut"
            },
            labels: categories,
            series: data,
            colors: [config.colors.primary, config.colors.secondary, config.colors.info, config.colors.success],
            stroke: {
                width: 5,
                colors: cardColor
            },
            dataLabels: {
                enabled: false,
                formatter: function(val, opt) {
                    return parseInt(val) + "%";
                }
            },
            legend: {
                show: false
            },
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    right: 15
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "75%",
                        labels: {
                            show: true,
                            value: {
                                fontSize: "1.5rem",
                                fontFamily: "Public Sans",
                                color: headingColor,
                                offsetY: -15,
                                formatter: function(val) {
                                    return parseInt(val) + "%";
                                }
                            },
                            name: {
                                offsetY: 20,
                                fontFamily: "Public Sans"
                            },
                            total: {
                                show: true,
                                fontSize: "0.8125rem",
                                color: legendColor,
                                label: categories[0],
                                formatter: function(w) {
                                    return Math.floor(w.globals.seriesTotals[0]) + "%";
                                }
                            }
                        }
                    }
                }
            },
            states: {
                active: {
                    filter: {
                        type: "none"
                    }
                }
            }
        };
    if (typeof chartOrderStatistics !== undefined && chartOrderStatistics !== null) {
        const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
        statisticsChart.render();
    }
}

// Activity Area Chart
// --------------------------------------------------------------------
function buildActivityChart(categories, data) {
    const activityAreaChartEl = document.querySelector("#activityChart"),
        activityAreaChartConfig = {
            chart: {
                height: 120,
                width: 220,
                parentHeightOffset: 0,
                toolbar: {
                    show: false
                },
                type: "area"
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            series: [
                {
                    data: data
                }
            ],
            colors: [config.colors.success],
            fill: {
                type: "gradient",
                gradient: {
                    shade: shadeColor,
                    shadeIntensity: 0.8,
                    opacityFrom: 0.8,
                    opacityTo: 0.25,
                    stops: [0, 85, 100]
                }
            },
            grid: {
                show: false,
                padding: {
                    top: -20,
                    bottom: -8
                }
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: categories,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        fontSize: "13px",
                        colors: labelColor
                    }
                }
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            tooltip: {
                y: {
                    title: {
                        formatter: function(seriesName) {
                            return "";
                        }
                    }
                }
            }
        };
    if (typeof activityAreaChartEl !== undefined && activityAreaChartEl !== null) {
        const activityAreaChart = new ApexCharts(activityAreaChartEl, activityAreaChartConfig);
        activityAreaChart.render();
    }
}

// Order Statistics Chart
// --------------------------------------------------------------------
const leadsReportChartEl = document.querySelector("#leadsReportChart"),
    leadsReportChartConfig = {
        chart: {
            height: 157,
            width: 130,
            parentHeightOffset: 0,
            type: "donut"
        },
        labels: ["Electronic", "Sports", "Decor", "Fashion"],
        series: [45, 58, 30, 50],
        colors: [
            chartColors.donut.series1,
            chartColors.donut.series2,
            chartColors.donut.series3,
            chartColors.donut.series4
        ],
        stroke: {
            width: 0
        },
        dataLabels: {
            enabled: false,
            formatter: function(val, opt) {
                return parseInt(val) + "%";
            }
        },
        legend: {
            show: false
        },
        tooltip: {
            theme: false
        },
        grid: {
            padding: {
                top: 15
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: "75%",
                    labels: {
                        show: true,
                        value: {
                            fontSize: "1.5rem",
                            fontFamily: "Public Sans",
                            color: headingColor,
                            fontWeight: 500,
                            offsetY: -15,
                            formatter: function(val) {
                                return parseInt(val) + "%";
                            }
                        },
                        name: {
                            offsetY: 20,
                            fontFamily: "Public Sans"
                        },
                        total: {
                            show: true,
                            fontSize: ".7rem",
                            label: "1 Week",
                            color: labelColor,
                            formatter: function(w) {
                                return "32%";
                            }
                        }
                    }
                }
            }
        }
    };
if (typeof leadsReportChartEl !== undefined && leadsReportChartEl !== null) {
    const leadsReportChart = new ApexCharts(leadsReportChartEl, leadsReportChartConfig);
    leadsReportChart.render();
}
