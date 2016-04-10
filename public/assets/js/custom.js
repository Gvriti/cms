$(function () {
    // Fix sidebar toggle when it has fixed position
    $('a[data-toggle="sidebar"]').on('click', function (e) {
        e.preventDefault();

        var expanded = $('#main-menu').find('.expanded');
        if (expanded.length) {
            if (public_vars.$sidebarMenu.hasClass('collapsed')) {
                $('> ul', expanded).css('display', 'block');
            } else {
                $('> ul', expanded).css('display', '');
            }
        }
    });

    // Toggle page action buttons
    $('#items').on('click', '.btn-toggle', function (e) {
        e.preventDefault();

        if (! $(this).hasClass('active')) {
            $('#items .btn-action').hide();
            $('#items .btn-toggle').removeClass('active');
        }

        $(this).siblings('.btn-action').toggle(300);
        $(this).addClass('active');
    });

    // Make form closable on "#submit-close" click
    $('#submit-close').on('click', function () {
        $('input.form-close').val(1);
    });

    // Disable buttons on submit for some period of time
    $(document).on('submit', 'form', function () {
        $('input[type="submit"], button[type="submit"]', this).prop('disabled', true);

        setTimeout(function (form) {
            $('input[type="submit"], button[type="submit"]', form).prop('disabled', false);
        }, 800, this);
    });

    // Delete form
    $('.form-delete').on('submit', function (e) {
        e.preventDefault();
        var perform = confirm('Are you sure you want to delete?');
        if (perform != true) return;
        var form = $(this);
        var formId = $(this).data('id');
        var btn = form.find('[type="submit"]').prop('disabled', true);

        var input = form.serialize();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: input,
            success: function (data, status, xhr) {
                if (data) {
                    // alert toastr message
                    toastr[data.result](data.message);

                    if (data.result == 'success') {
                        form.closest('#item' + formId).fadeOut(600, function () {
                            if ($(this).data('parent') == 1) {
                                $(this).closest('.uk-parent').removeClass('uk-parent');
                                disableParentDeletion(formId);
                            }

                            $(this).remove();
                        });
                    }
                }

                btn.prop('disabled', false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);

                alert(xhr.responseText);
            }
        });
    });

    // Ajax form submit
    var ajaxFormSelector = '.ajax-form';
    $(document).on('submit', ajaxFormSelector, function (e) {
        e.preventDefault();
        var form = $(this);
        var lang = form.data('lang');
        lang = lang ? lang : '';
        $('.form-group', form).find('.text-danger').remove();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function (data, status, xhr) {
                // toastr alert message
                if (typeof toastr == 'object') {
                    toastr[data.result](data.message);
                }
                // fill form inputs
                if (data.input && typeof data.input === 'object') {
                    $.each(data.input, function (index, element) {
                        var item = $('#' + index + lang, form);
                        if (item.val() != element) {
                            item.val(element);
                        }

                        if (item.data('type') == 'general') {
                            var inputGeneral = $(ajaxFormSelector + ' [name="' + index + '"]');
                            $(inputGeneral).each(function (i, e) {
                                item = $(e);
                                if (item.val() != element) {
                                    item.val(element);
                                    if (item.is(':checkbox')) {
                                        var bool = element == 1 ? true : false;
                                        item.prop('checked', bool);
                                    }
                                    if (item.is('select')) {
                                        item.trigger('change');
                                    }
                                }
                            });
                        }
                    });
                    form.trigger('ajaxFormSuccess', [data.input]);
                } else {
                    form.trigger('ajaxFormSuccess');
                }
                $('.form-group', form).removeClass('validate-has-error');
            },
            error: function (xhr) {
                form.trigger('ajaxFormError');

                if (xhr.status == 422) {
                    var data = xhr.responseJSON;

                    $.each(data, function (index, element) {
                        var field = $('#' + index + lang, form);
                        field.closest('.form-group').addClass('validate-has-error');

                        var errorMsg = '<div class="text-danger">'+element+'</div>';
                        if (! field.next().length && ! field.prev().length) {
                            field.after(errorMsg);
                        } else {
                            field.parent().after(errorMsg);
                        }
                    });

                    if (form.hasClass('.validate-has-error')) {
                        $('html, body').animate({
                            scrollTop: $('.validate-has-error').offset().top - 100
                        }, 400);
                    }
                } else {
                    alert(xhr.responseText);
                }
            },
            complete: function (xhr) {
                form.trigger('ajaxFormComplete');
            }
        });
    });

    // Visibility request
    $('form.visibility').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);

        $.post(form.attr('action'), form.serialize(), function (data) {
            if (data) {
                if (data.visible) {
                    var icon = 'fa-eye';
                    var removeClass = 'btn-gray';
                    var addClass = 'btn-white';
                } else {
                    var icon = 'fa-eye-slash';
                    var removeClass = 'btn-white';
                    var addClass = 'btn-gray';
                }
                item.removeClass(removeClass)
                    .addClass(addClass)
                    .find('span')
                    .attr('class', icon);
            }
        }, 'json').fail(function (xhr) {
            alert(xhr.responseText);
        });
    });
});

// Lockscreen event handlers and functions
var timer;
var timerIsActive = true;

$('form#set-lockscreen').on('submit', function (e) {
    e.preventDefault();

    clearTimeout(timer);
    timerIsActive = false;

    setLockscreen($(this).attr('action'));
});

function setLockscreen(url) {
    var input = {'_method':'put', '_token':csrf_token()};

    $.post(url, input, function (data) {
        if (data) {
            $('body').append(data.view);
            $('body').addClass('lockscreen-page');
        }
    }, 'json').fail(function (xhr) {
        alert(xhr.responseText);
    });
}

function lockscreen(time, url, reActive) {
    if (reActive) {
        timerIsActive = true;
    }

    $(document).on('click mousemove keypress scroll', function () {
        if (timerIsActive) {
            clearTimeout(timer);

            timer = setTimeout(function () {
                setLockscreen(url);

                timerIsActive = false;
            }, time);
        }
    });

    $(document).trigger('mousemove');
}
// Lockscreen end

// Update url recursively
function updateUrl(target, url) {
    var prevUrl = url;

    target.each(function () {
        var item = $(this).find('a.link');

        url = prevUrl + '/' + item.data('slug');

        item.attr('href', url);

        if ($(this).hasClass('uk-parent')) {
            updateUrl($('> ul', this).children('li'), url);
        }
    });
}

function disableParentDeletion() {
    $('#nestable-list .form-delete [type="submit"]').prop('disabled', false);

    $('#nestable-list .uk-parent').each(function () {
        id = $(this).data('id');
        $('.form-delete[data-id="' + id + '"] [type="submit"]', this).prop('disabled', true);
    });
}

function positionable(url, orderBy, page, hasMorePages) {
    var saveBtn = $('#save-tree');
    var saveBtnIcon = $('.icon-var', saveBtn);
    var postHiddens = {'_token':csrf_token()};

    if (page) {
        var aTagStart = '<a href="#" class="move btn btn-gray fa-long-arrow-';
        var aTagNext = 'right right" data-move="next" title="Move to next page"';
        var aTagPrev = 'left left" data-move="prev" title="Move to prev page"';
        var aTagEnd = '></a>';

        if (hasMorePages) {
            $('#nestable-list .btn-action').prepend(aTagStart + aTagNext + aTagEnd);
        }
        if (page > 1) {
            $('#nestable-list .btn-action').prepend(aTagStart + aTagPrev + aTagEnd);
        }
    }

    $('#nestable-list').on('nestable-stop', function (e) {
        $('#nestable-list .move').remove();
        saveBtn.show().prop('disabled', false);
        saveBtnIcon.removeClass('fa-spin fa-check').addClass('fa-save');
    });

    // Position move
    $('#nestable-list').on('click',  'a.move', function (e) {
        e.preventDefault();
        var move = $(this).data('move');
        var item = $(this).closest('li');
        var input = [{'id':item.data('id'), 'pos':item.data('pos')}];

        if (move == 'next') {
            var items = item.nextAll();
        } else {
            var items = item.prevAll();
        }

        items.each(function (i, e) {
            input.push({'id':$(e).data('id'), 'pos':$(e).data('pos')});
        });

        var input = $.extend({'data':input, 'move':move, 'orderBy':orderBy}, postHiddens);

        $.post(url, input, function (data) {
            page = move == 'next' ? page + 1 : page - 1;
            var href = window.location.href;
            var hrefQueryStart = href.indexOf('?');
            if (hrefQueryStart > 1) {
                href = href.substr(0, hrefQueryStart);
            }
            window.location.href = href + '?page=' + page;
        }, 'json').fail(function (xhr, status, error) {
            alert(xhr.responseText);
        });
    });

    // Position save
    saveBtn.on('click', function () {
        $(this).prop('disabled', true);
        saveBtnIcon.addClass('fa-spin');

        if (page) {
            $('#nestable-list .move').remove();
            if (hasMorePages) {
                $('#nestable-list .btn-action').prepend(aTagStart + aTagNext + aTagEnd);
            }
            if (page > 1) {
                $('#nestable-list .btn-action').prepend(aTagStart + aTagPrev + aTagEnd);
            }
        }

        var input = $('#nestable-list').data('nestable').serialize();

        if (orderBy) {
            var posArr = [];
            $(input).each(function (i, e) {
                posArr[i] = e.pos;
            });
            if (orderBy == 'desc') {
                posArr.sort(function (a, b) {return b-a});
            } else {
                posArr.sort(function (a, b) {return a-b});
            }
            $(posArr).each(function (i, e) {
                input[i].pos = e;
            });
        }

        input = {'data':input};

        input = $.extend(input, postHiddens);
        $.post(url, input, function (data) {
            saveBtnIcon.removeClass('fa-spin fa-save').addClass('fa-check');

            if (orderBy) {
                $(input.data).each(function (i, e) {
                    $('#nestable-list #item'+e.id).data('pos', e.pos);
                });
            }

            saveBtn.trigger('positionSaved');

            disableParentDeletion();
        }, 'json').fail(function (xhr) {
            saveBtnIcon.removeClass('fa-spin fa-save').addClass('fa-remove');

            alert(xhr.responseText);
        }).always(function () {
            saveBtn.delay(400).fadeOut(500);
        });
    });
}
