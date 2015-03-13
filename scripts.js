function print_r(arr, level) {
    var print_red_text = "";
    if(!level) level = 0;
    var level_padding = "";
    for(var j=0; j<level+1; j++) level_padding += "    ";
    if(typeof(arr) == 'object') {
        for(var item in arr) {
            var value = arr[item];
            if(typeof(value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value,level+1);
            }
            else
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    }

    else  print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}

 $(function change_h (){
    var l = ($('.pagination a')['length'])-2;
     var h;
     //if (l>60)
     //if (l>40) h='220';
     //if (l >20) h='110';

     switch (true) {
         case l< 21:
             h='70';break;
         case l > 20 && l < 41:
             h='110';break;
         case l > 40 && l < 61:
             h='110';break;
         default:
             h='222';break;
     }
    //$('.pagination').height(h);
    //alert(h);
     //alert(($('.pagination a')['length']));


    //$('#block1').hide();
//
})


// Пагинация
$(function() {

    $('.test-data').find('div:first').show();

    $('.pagination a').on('click', function () {
        if ($(this).attr('class') == 'nav-active') return false;
        // Управление выделением № вопроса
        // Определяем номер страницы пагинации, на которую нажали, по id находим элемент и его выделяем

        //var link = $(this).attr('href'); // ссылка на текст вкладки для показа
        var prevActive = $('.pagination > a.nav-active').attr('href'); // ссылка на текст пока что активной вкладки
        var link1 = $(this).attr('id'); // ссылка на текст вкладки для показа
        var prevActive1 = $('.pagination > a.nav-active').attr('id');
        var n;

        if (link1 == "page-0") { // Перемещение на предыдущий элемент
            n = (prevActive1.substring(5) - 1);
            if (n == 0)return false;
            link1 = "page-" + n; // определяем id новой открываемой страницы
            delete n;
        }
        if (link1 == "page-00") { // Перемещение на следующий элемент {
             n = (parseInt(prevActive1.substring(5)) + 1);
            if (n == ($('.pagination a')['length']) - 1)return false;
            link1 = "page-" + n; // определяем id новой открываемой страницы
            delete n;
        }
        ;

        $('.pagination > a.nav-active').removeClass('nav-active'); // удаляем класс активной ссылки
        var t = document.getElementById(link1);
        if (t)  $(t).addClass("nav-active"); // добавляем класс активной вкладки
        link = ($(t).attr('href'));
        delete t;

        //управляем активностью ссылок - показываем выбранный вопрос
        // скрываем/показываем вопросы
        $(prevActive).fadeOut(100, function () {
            $(link).fadeIn(100);
        });
        return false;

    });


    // Возможность для пользователя пометить вопросы красным, чтобы вернуться к ним позже
    $('.pagination a').dblclick ( function() {
        var link = ($(this).attr('id'));
        var obj =   $(document.getElementById(link));
        obj.toggleClass("red-active");
        delete obj,link;

    });


    //Пройденный вопрос отмечаем как отвеченный в пагинации
    $('.a').click ( function() {
        $('.pagination > a.nav-active') .css ( {
            "text-decoration": "line-through"
               }
        ) ;
        //$('.pagination').height("600");
    });


// Закончить тест
    $('#btn').click(function(){

        var test = +$('#test-id').text();
        var skill = +$('#skill-id').text();
        var time_id = +$('#time-id').text();

        var res = {'test':test};
        res ['skill'] =skill;
        res['time_id'] = time_id;
        var $error=0;

        $('.question').each(function(){
            var id = $(this).data('id');
            var obj = $('input[name=question-' + id + ']:checked'); //выбираем выбранную радиокнопку
            var objt = $('input[name=text-' + id + ']:text').val();

            if (!obj.val()) {

                $error=1;
                //alert("NO VAL");
                res[id] = 0;
            } else

                res[id] = {
                'answer': obj.val(),
                'correct_answer': obj.attr('correct_answer')

            };

        });

        //if ($error == 1) {
        //    if (confirm("Вы не ответили на вопрос." + $('.question').attr(id) + " Продолжить?")) {
        //
        //    } else {
        //
        //    }
        //};

        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: res,
            success: function(html){
                $('.content').html(html);
            },
            error: function(){
                alert('Error!');
            }
        });

    });


// Показать результаты пользователя

    $("#btn2").click(function(){

        //При нажатии на кнопку показывает/скрывает результаты прошлых тестов
        var param = document.getElementById('last_result1').style.display;
        //$('.last_result1').style.display='block';
        //document.getElementById('last_result1').style.display='block';
        if (param != 'block') {
            $('#wrap1').hide();
            $('#block1').hide();
            //$('#last_result1').show();
            document.getElementById('last_result1').style.display = 'block';
        } else {

            $('#wrap1').show();
            $('#block1').show();
            //$('#last_result1').hide();
            document.getElementById('last_result1').style.display = 'none';
        }
    });
});