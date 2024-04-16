let temp_lang = JSON.parse(var_lang);

function getRandonColor() {
    var rand = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
    var color = '#' + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)] + rand[Math.ceil(Math.random() * 15)];
    return color;
}

let XV = [];
let YV = [];
let CX = [];
let tt = temp_lang.tempp;
for (var i in tt) {
    XV.push(tt[i].name);
    YV.push(tt[i].count);
    CX.push(getRandonColor());
}

var xValues = XV;
var yValues = YV;
var barColors = CX;

new Chart("myChart", {
    type: "doughnut",
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