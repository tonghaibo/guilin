/*
���ã�ͼƬ����Ԥ��
ʱ�䣺2011-12-23
���ߣ��±�
*/
jQuery.fn.PreviewImage = function(divId, imgId, width, height) {
    if (width == null)
        width = 100;
    if (height == null)
        height = 100;
    var file = $(this);
    var fileId = file.attr("id");
    file.change(function() {
        var extend = file.val().substring(file.val().lastIndexOf(".") + 1);
        var suffix = new Array("jpg", "jpeg", "gif", "png", "bmp");
        var sum = 0;
        for (var i = 0; i < suffix.length; i++) {
            if (extend.toLowerCase() == suffix[i].toLowerCase()) {
                sum = 1;
            }
        }
        if (sum == 0) {
            alert('�ϴ�ͼƬ���ͽ�֧��[.jpg|.jpeg|.gif|.png|.bmp]');
            return false;
        }
        if (window.navigator.userAgent.indexOf("MSIE 8.0") >= 1) {
            var imgFile = document.getElementById(fileId);
            var newPreview = document.getElementById(divId);
            newPreview.innerHTML = "";
            var imgDiv = document.createElement("div");
            newPreview.appendChild(imgDiv);

            imgDiv.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod = scale);border:solid 1px #cccccc;padding:2px;";
            imgDiv.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgFile.value;
            imgDiv.style.width = width + "px";
            imgDiv.style.height = height + "px";
        } else {
            try {
                document.getElementById(imgId).src = getFullPath(document.getElementById(fileId));
            } catch (err) {
                $("#" + divId).html("�Բ���ͼƬԤ�������ݲ�֧�ָ������!");
            }
        }
    });

    //����IE6 7 ���
    function getFullPath(obj) {
        //�õ�ͼƬ������·��
        if (obj) {
            //ie
            if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
                obj.select();
                return document.selection.createRange().text;
            }
            //firefox
            else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {
              
                if (obj.files) {
                    return obj.files.item(0).getAsDataURL();
                }
                return obj.value;
            }
            return obj.value;
        }
    }
}