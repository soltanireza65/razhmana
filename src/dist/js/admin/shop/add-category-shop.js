

Dropzone.autoDiscover = false;


let BrandCat = $('#category-cat-select')
let BrandImg = []

$(document).ready(function () {

    BrandCat.select2({
        placeholder: lang_vars.categories,

    })


    var dropzoneBrand = new Dropzone('#brand-dz', {
        url: '/upload',
        previewTemplate: document.querySelector('#preview-template-brand').innerHTML,
        parallelUploads: 5,
        acceptedFiles: "image/*",
        autoQueue: true,
        previewsContainer: "#preview-template-brand",
        addRemoveLinks: true,
        autoProcessQueue: true,
        maxFiles: 1,
        dictCancelUpload: "لغو",
        thumbnailMethod: "contain",
        dictRemoveFile: "حذف",
        dictCancelUploadConfirmation: "آیا از لغو بارگذاری اطمینان دارید؟",
        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                file.previewElement.classList.remove("dz-file-preview");
                var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                for (var i = 0; i < images.length; i++) {
                    var thumbnailElement = images[i];
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                }
                setTimeout(function () {
                    file.previewElement.classList.add("dz-image-preview");
                }, 1);
            }
        },
        init: function () {
            this.on("maxfilesexceeded", function (file) {
                this.removeFile(file);
                $("#brand-error").html("<div style='color: red'>بیش از 1 عکس نمیتوانید انتخاب کنید</div>");
            });
            this.on("complete", function (file) {
                if (!file.type.match('image.*')) {
                    this.removeFile(file);
                    $("#brand-error").html("<div style='color: red'>فقظ فایل عکس مورد قبول میباشد</div>");
                    return false;
                }

            });
            this.on('success', async function (file) {
                if (file.accepted) {
                    BrandImg.push(file.dataURL);

                }
            });
            this.on('removedfile', async function (file) {
                const index = BrandImg.indexOf(file.dataURL);
                if (index > -1) {
                    BrandImg.splice(index, 1);
                }




            });

            // console.log(JSONproductInfo.product_images)
            // let loop = JSON.parse(productInfo.product_images);
            // for (let i = 0; i < loop.length; i++) {
            //     console.log(loop[i])
            //     var imageUrl = '';
            //     if (loop[i].includes('https://')) {
            //         imageUrl = loop[i];
            //     } else {
            //         imageUrl = "https://nkala.local" + loop[i];
            //     }
            //
            //     var filename = getFileNameFromUrl(imageUrl);
            //     var xhr = new XMLHttpRequest();
            //     xhr.open("GET", imageUrl, true);
            //     xhr.responseType = "blob";
            //     xhr.onload = function () {
            //         if (xhr.status === 200) {
            //             var blob = xhr.response;
            //             var file = new File([blob], filename, {type: blob.type});
            //             dropzone4.addFile(file);
            //         }
            //     };
            //     xhr.send();
            // }
        }

    });
})