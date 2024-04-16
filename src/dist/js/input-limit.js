$(".mj-text-inputs-mal").keypress(function (e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if ((charCode > 31 && charCode < 48) || (charCode > 57 && charCode < 65) || (charCode > 90 && charCode < 97) || (charCode > 122 && charCode < 254)) {
        return false;
    }
    let txt= $(".mj-text-inputs-mal").val()
     if (txt.includes('<script>')){
         return false;
     }
    return true;
});