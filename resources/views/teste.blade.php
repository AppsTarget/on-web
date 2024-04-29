

<form method='post' action='{{ url('sendemail/send') }}'>
    {{ csrf_field() }}
    <div class='form-group'>
        <label>Enter Your Name</label>
        <input type='text' name='name' class='form-control'>
    </div>

    <div class='form-group'>
        <label>Enter Your Email</label>
        <input type='text' name='email' class='form-control'>
    </div>

    <div class='form-group'>
        <label>Enter Your Message</label>
        <textarea type='text' name='message' class='form-control'>
        </textarea>
    </div>

    <div class='form-group'>
        <input type='submit' name='send' value='send' class='bttn btn-info'>
    </div>
</form>