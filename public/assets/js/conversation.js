window.onload = () => {
    //reload messages
    setInterval(function(){
        $("#reload_messages").load(window.location.href + " #reload_messages" );
    }, 1000);

}
