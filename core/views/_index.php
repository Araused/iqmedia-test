<div class="row">
    <div>
        <div class="col">
            <label class="mb-2 form-label">Введите исходный URL целиком для получения краткого варианта ссылки</label>
            <div class="input-group">
                <input type="text"
                       placeholder="https://a.very.very.very.looooooooooong.url"
                       class="form-control"
                       id="url"
                       value="">
                <button id="sendAjax" class="btn btn-primary">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<script>
    let ajaxHandler = (longUrl) => {
        let fetchStatus;

        fetch('/create-shortlink?url=' + longUrl, {
            method: "POST"
        })
            .then((response) => {
                fetchStatus = response.status;

                return response.text();
            })
            .then((data) => {
                let div = document.getElementById('forAjaxContent');
                div.innerHTML = data;
            })
            .catch((error) => {
                console.log(error);
            });
    };

    document.addEventListener('click', (event) => {
        if (!event.target.matches('#sendAjax')) {
            return;
        }

        event.preventDefault();

        let input = document.getElementById("url");
        let longUrl = input.value;

        if (longUrl.length > 0) {
            ajaxHandler(longUrl);
            input.classList.remove('is-invalid');
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-invalid');
        }
    }, false);
</script>