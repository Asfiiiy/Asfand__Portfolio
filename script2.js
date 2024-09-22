ticket.addEventListener("mousemove", e => {
    const ticketElm = document.getElementById("ticket")
    const { x, y, width, height } = ticketElm.getBoundingClientRect()
    console.log(ticketElm.getBoundingClientRect())
    const centerPoint = { x: x + width / 2, y: y + height / 2 }
    ticket.addEventListener("mousemove", e => {
        const degreeX = (e.clientY - centerPoint.y) * 0.1
        const degreeY = (e.clientX - centerPoint.x) * -0.1

        ticketElm.style.transform = `perspective(1000px) rotateX(${degreeX}deg) rotateY(${degreeY}deg)`
    })
})



// document.addEventListener("DOMContentLoaded", function() {
//     const navToggle = document.querySelector(".nav-toggle");
//     const navList = document.querySelector(".nav-list");

//     navToggle.addEventListener("click", function() {
//         navList.classList.toggle("active");
//     });
// });


document.querySelector('.nav-toggle').addEventListener('click', function () {
    document.querySelector('.nav-list').classList.toggle('active');});


   //////////////////////loader////////////////

    window.addEventListener('load', function() {
        // Set a time delay before hiding the preloader
        setTimeout(function() {
            document.getElementById('preloader').style.display = 'none'; // Hide preloader
            document.querySelector('.wrapper').style.display = ''; // Show content
        }, 5000); // Delay in milliseconds (3000ms = 3 seconds)
    });
    
//////////////////////////////nav///////////////////////////////
// document.querySelector('.nav-toggle').addEventListener('click', function() {
//     document.querySelector('.nav-list').classList.toggle('active');
// });

document.querySelector('.nav-toggle').addEventListener('click', function() {
    document.querySelector('.nav-list').classList.toggle('active');
});



    /////////////////// formi //////////////////////////
   
    
    
    //   var isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
                 


// document.addEventListener("DOMContentLoaded", function() {
//     var isAdmin = php echo json_encode($isAdmin); 
//     if (isAdmin) {
//         document.querySelector('.formi').style.display = 'block';
//     }
// });
    