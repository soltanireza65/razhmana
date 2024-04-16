function printContent() {
    $(".printDIVs").printThis({
        importCSS: true,            // import parent page css
        importStyle: true,         // import style tags
        loadCSS: "sssas",                // path to additional css file - use an array [] for multiple
        pageTitle: "ss",              // add title to print page
        canvas: true,              // copy canvas content
    });
}