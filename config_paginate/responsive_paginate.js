window.addEventListener('resize' , () => {
    load();
    })
    
    load();
    function load() {
        var ul = document.getElementById('ul-paginate-responsive').classList;
        if(innerWidth < 550) {
          
            ul.add('pagination-sm')
        }
        else if(innerWidth > 1240) {
            ul.add('pagination-lg')
        }
        else {
            if(ul.contains('pagination-sm')) {
                ul.remove('pagination-sm');
            }
            if(ul.contains('pagination-lg')) {
                ul.remove('pagination-lg');
            }
        }
    }