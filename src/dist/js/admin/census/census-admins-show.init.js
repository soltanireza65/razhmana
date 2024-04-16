let temp_lang = JSON.parse(var_lang);


$.each(temp_lang, function (key, value) {
    setChart(value.chart, value.values)
});


function getRandonColor() {
    var rand = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
    var color = '#' + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)];
    return color;
}

function setChart(chartName, Valuse) {

    let XV = [];
    let YV = [];
    let CX = [];
    let tt = Valuse;
    for (var i in tt) {
        XV.push(tt[i].adminName);
        YV.push(tt[i].count);
        CX.push(getRandonColor());
    }

    var xValues = XV;
    var yValues = YV;
    var barColors = CX;

    new Chart(chartName, {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: false,
                text: "World Wide Wine Production 2018"
            },
            legend: {
                labels: {
                    // This more specific font property overrides the global property
                    fontColor: '#96a6b5',
                    fontFamily: 'iransans',
                    fontSize: 12,
                }
            }
        }
    });


}

function printContent() {
    $(".printDIVs").printThis({
        importCSS: true,            // import parent page css
        importStyle: true,         // import style tags
        loadCSS: "sssas",                // path to additional css file - use an array [] for multiple
        pageTitle: "ss",              // add title to print page
        canvas: true,              // copy canvas content
    });
}
