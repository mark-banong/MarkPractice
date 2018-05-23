function setIcon(label, url) {
    label.style.display = 'block';
    label.style.width = '43px';
    label.style.height = '46px';
    label.style.margin = '0 15px 0 0';
    label.style.backgroundImage = url;
    label.style.backgroundRepeat = "no-repeat";
    label.style.float = "left";
    label.innerHTML = "";
}

function resetButton(input, label) {
    (function($) {
        var labelDOM = label[0];
        var rating = $(input).val().toLowerCase();

        if (rating === 'mycket daligt' || rating === '1') {
            //call a method that will change back, all the label to its original icon
            input.checked = false;
            setIcon(labelDOM, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile01.jpg)');
        } else if (rating === 'daligt' || rating === '2') {
            input.checked = false;
            setIcon(labelDOM, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile02.jpg)');

        } else if (rating === 'tillfreds-stallande' || rating === '3') {
            input.checked = false;
            setIcon(labelDOM, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile03.jpg)');

        } else if (rating === 'bra' || rating === '4') {
            input.checked = false;
            setIcon(labelDOM, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile04.jpg)');

        } else if (rating === 'mycket bra' || rating === '5') {
            input.checked = false;
            setIcon(labelDOM, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile05.jpg)');
        }
    }(jQuery));
}

function changeIcon(id, rating, inputId) {
    (function($) {

        var label = document.getElementById(id);
        var input = $('#' + inputId);

        $(input).parent().siblings().each(function(index) {
            resetButton($(this).find('input').first(), $(this).find('label').first());
        });

        if (rating === 'mycket daligt' || rating === '1') {
            //call a method that will change back, all the label to its original icon
            if (input.checked) {
                input.checked = false;
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile01.jpg)');
            } else {
                input.checked = true;
                input.click();
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smileBW01.jpg)');
            }
        } else if (rating === 'daligt' || rating === '2') {
            if (input.checked) {
                input.checked = false;
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile02.jpg)');
            } else {
                input.checked = true;
                input.click();
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smileBW02.jpg)');
            }
        } else if (rating === 'tillfreds-stallande' || rating === '3') {
            if (input.checked) {
                input.checked = false;
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile03.jpg)');
            } else {
                input.checked = true;
                input.click();
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smileBW03.jpg)');
            }
        } else if (rating === 'bra' || rating === '4') {
            if (input.checked) {
                input.checked = false;
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile04.jpg)');
            } else {
                input.checked = true;
                input.click();
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smileBW04.jpg)');
            }
        } else if (rating === 'mycket bra' || rating === '5') {
            if (input.checked) {
                input.checked = false;
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile05.jpg)');
            } else {
                input.checked = true;
                input.click();
                setIcon(label, 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smileBW05.jpg)');
            }
        }

        console.log(input.checked);
        
    }(jQuery));
}


(function($) {
    var labelId = [];

    $(document).ready(function() {


        var myNodeList = document.querySelectorAll(".rating ul label");
        var inputs = document.querySelectorAll(".rating ul input");

        for (var count = 0; count < myNodeList.length; count++) {
            myNodeList[count].setAttribute('id', 'rating-' + count);
            inputs[count].setAttribute('id', 'rating-input-' + count);

            $(inputs[count]).addClass('hidden');

            if (myNodeList[count].textContent.toLowerCase() === 'mycket daligt' || myNodeList[count].textContent.toLowerCase() === '1') {
                console.log(myNodeList[count].textContent);
                // myNodeList[count].setAttribute('id', 'mycketdaligt');
                setIcon(myNodeList[count], 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile01.jpg)');
                myNodeList[count].setAttribute('onClick', "changeIcon( 'rating-" + count + "', 'mycket daligt', 'rating-input-" + count + "')");

            } else if (myNodeList[count].textContent.toLowerCase() === 'daligt' || myNodeList[count].textContent.toLowerCase() === '2') {

                setIcon(myNodeList[count], 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile02.jpg)');
                myNodeList[count].setAttribute('onClick', "changeIcon( 'rating-" + count + "', 'daligt', 'rating-input-" + count + "')");

            } else if (myNodeList[count].textContent.toLowerCase() === 'tillfreds-stallande' || myNodeList[count].textContent.toLowerCase() === '3') {
                setIcon(myNodeList[count], 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile03.jpg)');
                myNodeList[count].setAttribute('onClick', "changeIcon( 'rating-" + count + "', 'tillfreds-stallande', 'rating-input-" + count + "')");
            } else if (myNodeList[count].textContent.toLowerCase() === 'bra' || myNodeList[count].textContent.toLowerCase() === '4') {
                setIcon(myNodeList[count], 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile04.jpg)');
                myNodeList[count].setAttribute('onClick', "changeIcon( 'rating-" + count + "', 'bra', 'rating-input-" + count + "')");
            } else if (myNodeList[count].textContent.toLowerCase() === 'mycket bra' || myNodeList[count].textContent.toLowerCase() === '5') {
                setIcon(myNodeList[count], 'url(/wp-content/plugins/sefab-plugin/inc/Base/img/smile05.jpg)');
                myNodeList[count].setAttribute('onClick', "changeIcon( 'rating-" + count + "', 'mycket bra', 'rating-input-" + count + "')");
            }
        }
    });
}(jQuery));