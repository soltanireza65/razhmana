const rangeInput = document.querySelectorAll(".mj-price-filter-range .range-input input");
let rangeResult = document.querySelectorAll(".mj-price-filter-range .range-result span");
const Progress = document.querySelector(".mj-price-filter-range .slider .progress");

rangeResult.toLocaleString('ar-EG');

let priceGap = 1000000;

rangeInput.forEach(input =>{
    input.addEventListener("input" , e=>{
        let minVal = parseInt(rangeInput[0].value),
            maxVal = parseInt(rangeInput[1].value);

        if(maxVal - minVal < priceGap){
            if(e.target.className === 'range-min'){
                rangeInput[0].value = maxVal - priceGap;
            }else{
                rangeInput[1].value = minVal + priceGap;
            }
        }else{
            rangeResult[1].innerHTML = minVal.toLocaleString('ar-EG');
            rangeResult[3].innerHTML = maxVal.toLocaleString('ar-EG');
            Progress.style.left = (minVal / rangeInput[0].max)*100 + "%"
            Progress.style.right = 100 - (maxVal / rangeInput[1].max)*100 + "%"

        }

    })
})

//----karkard
const rangeInput2 = document.querySelectorAll(".mj-use-filter-range .range-input input");
const rangeResult2 = document.querySelectorAll(".mj-use-filter-range .range-result span");
const Progress2 = document.querySelector(".mj-use-filter-range .slider .progress");

rangeResult2.toLocaleString('ar-EG');

let priceGap2 = 100;

rangeInput2.forEach(input =>{
    input.addEventListener("input" , e=>{
        let minVal = parseInt(rangeInput2[0].value),
            maxVal = parseInt(rangeInput2[1].value);

        if(maxVal - minVal < priceGap2){
            if(e.target.className === 'range-min'){
                rangeInput2[0].value = maxVal - priceGap2;
            }else{
                rangeInput2[1].value = minVal + priceGap2;
            }
        }else{
            rangeResult2[1].innerHTML = minVal.toLocaleString('ar-EG');
            rangeResult2[3].innerHTML = maxVal.toLocaleString('ar-EG');
            Progress2.style.left = (minVal / rangeInput2[0].max)*100 + "%"
            Progress2.style.right = 100 - (maxVal / rangeInput2[1].max)*100 + "%"
        }
    })
})

//----year
init_range_input3()
function init_range_input3() {
    const rangeInput3 = document.querySelectorAll(".mj-year-filter-range .range-input input");
    const rangeResult3 = document.querySelectorAll(".mj-year-filter-range .range-result span");
    const Progress3 = document.querySelector(".mj-year-filter-range .slider .progress");


    let priceGap3 = 2;

    rangeInput3.forEach(input =>{
        input.addEventListener("input" , e=>{
            let minVal = parseInt(rangeInput3[0].value),
                maxVal = parseInt(rangeInput3[1].value),
                firstVal =parseInt(rangeInput3[0].min),
                lastVal =parseInt(rangeInput3[1].max);

            let X = lastVal - firstVal;
            let pEr = (1 / X)*100;

            console.log(minVal , rangeInput3[0].min , pEr)
            if(maxVal - minVal < priceGap3){
                if(e.target.className === 'range-min'){
                    rangeInput3[0].value = maxVal - priceGap3;
                }else{
                    rangeInput3[1].value = minVal + priceGap3;
                }
            }else{
                rangeResult3[1].innerHTML = minVal;
                rangeResult3[3].innerHTML = maxVal;
                Progress3.style.left = ((minVal - firstVal)*(pEr)) + "%"
                Progress3.style.right = ((lastVal - maxVal)*(pEr)) + "%"
            }
        })
    })
}