@extends('admin.layout')

@section('content')
<style>
.rcon-module {
    background: #222;
    border-radius: 4px;
}
.rcon-module .console {
    height: 60vh;
    min-height: 400px;
    max-height: 600px;
    padding: 10px;
    overflow: auto;
}
.rcon-module .console .console-out {
    color: #EAEAEA;
    border-bottom: 1px solid rgba(153, 153, 153, 0.38);
    display: block;
    margin-bottom: 5px;
    font-family: 'Ubuntu', Arial, serif;;
}
.rcon-module .message-box {
    padding: 10px;
    background: #222;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}
.rcon-module .message-box .input-group {
    margin-bottom: 10px;
}
.input-group-addon, .input-group-btn {
    width: 100%;
}
</style>
    <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,400italic,700,700italic' rel="stylesheet"
          type="text/css">
    <form method="get">
        @csrf
        <label for="server" style="padding: 0 12px;">{{  __('Server') }}</label>
        <div class="input-group label-floating">
            <select id="server" name="server" class="selectpicker" data-style="select-with-transition"
                    title="@lang('Select a server')!" data-size="7">
                <option disabled> @lang('Select a server')</option>
                @foreach ($servers as $serv)
                    <option value="{{ $serv->id }}" @if($serv->id === $server->id) selected @endif>
                        {{ $serv->name }}
                    </option>
                @endforeach
            </select>
            <span class="input-group-btn">
			<button type="submit" class="btn btn-danger"> {{ __('Select') }}</button>
		</span>
        </div>
    </form>
    <div class="rcon-module">
        <div class="console" id="cconsole">
            {!! $firstOut !!}
        </div>
        <div class="message-box">
            <div class="input-group form-group label-floating" style="">
                <label for="message" class="control-label" style="color: #9A9A9A">{{ __('Enter a command') }}</label>
                <input type="text" id="message" name="message" class="form-control" @if(!$enabled) disabled @endif>
                <span class="input-group-btn" style="width: 1%;">
				<button type="button" class="btn btn-default btn-round" onclick="send()" @if(!$enabled) disabled @endif><i
                        class="material-icons">send</i> @lang('Send')!</button>
			</span>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var server = {{ $server->id }};

        function send() {
            $.ajax({
                type: 'post',
                url: '/admin/rcon/sendCommand',
                data: {'command': $('#message').val(), 'server': server},
                response: 'text',
                success: function (data) {
                    $('#cconsole').append('<div class="console-out">' + data + '</div>');
                    $('#cconsole').scrollTop($('#cconsole')[0].scrollHeight);
                    $('#message').val('');
                }
            });
        }

        $("#message").keyup(function (event) {
            if (event.keyCode == 13) {
                send();
            }
        });
    </script>
@endsection
