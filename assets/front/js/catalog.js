

function lazy (){
    $(".lazy").Lazy({
        beforeLoad: function(element) {
            var imageSrc = element.data('src');
            console.log('image "' + imageSrc + '" is about to be loaded');
        },
        scrollDirection: 'vertical',
        effect: "fadeIn",
        effectTime: 250,
        threshold: 300,
        visibleOnly: false,  
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        }
    });
}

lazy();

