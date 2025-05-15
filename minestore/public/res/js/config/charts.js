let cardColor, headingColor, labelColor, shadeColor, borderColor, legendColor, heatMap1, heatMap2, heatMap3, heatMap4;

if (document.documentElement.classList.contains('dark-style')) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    borderColor = config.colors_dark.borderColor;
    legendColor = config.colors_dark.bodyColor;
    shadeColor = 'dark';
    heatMap1 = '#4f51c0';
    heatMap2 = '#595cd9';
    heatMap3 = '#8789ff';
    heatMap4 = '#c3c4ff';
} else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;
    legendColor = config.colors.bodyColor;
    shadeColor = '';
    heatMap1 = '#e1e2ff';
    heatMap2 = '#c3c4ff';
    heatMap3 = '#a5a7ff';
    heatMap4 = '#696cff';
}

// Donut Chart Colors
const chartColors = {
    donut: {
      series1: config.colors.success,
      series2: '#99E570',
      series3: '#B5EC97',
      series4: '#E3F8D7'
    }
};
