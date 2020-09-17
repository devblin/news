<?php
require __DIR__ . "/autoload.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/70ccd1e73b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>News</title>
</head>

<body>
    <div class="container-fluid p-0 m-0" style="overflow-x: hidden;">
        <input id="baseurl" type="hidden" value=<?php echo env("BASE_URL") . env("FOLDER"); ?>>

        <nav id="navmy" class="navbar navbar-expand bg-dark ">
            <a id="newscountry" class="navbar-brand" href="#">News</a>
            <input id="searchbar" class="form-control" type="text" placeholder="Search for any topic">
            <button id="searchbtn" class="ml-1 p-auto btn btn-primary"><i class="f20 fas fa-search"></i></button>
        </nav>

        <div class="p-2 d-flex justify-content-center" style="flex-wrap: wrap;">
            <span role="button" data-type="" class="topic f20 m-1 badge badge-dark">All</span>
            <span role="button" data-type="business" class="topic f20 m-1 badge badge-primary">business</span>
            <span role="button" data-type="entertainment"
                class="topic f20 m-1 badge badge-secondary">entertainment</span>
            <span role="button" data-type="general" class="topic f20 m-1 badge badge-success">general</span>
            <span role="button" data-type="health" class="topic f20 m-1 badge badge-danger">health</span>
            <span role="button" data-type="science" class="topic f20 m-1 badge badge-warning">science</span>
            <span role="button" data-type="sports" class="topic f20 m-1 badge badge-info">sports</span>
            <span role="button" data-type="technology" class="topic f20 m-1 badge badge-light">technology</span>
        </div>
        <div class="container p-5" style="overflow: hidden;">
            <ul id="newsresults" class="list-group w-100 p-0 m-0">
            </ul>
            <ul class="pagination justify-content-center mb-3">
                <li data-pg=0 class="page-item prev"><a class="page-link" href="#p"><i
                            class="fas fa-arrow-circle-left"></i>
                        Previous</a></li>
                <li data-pg=10 class="page-item next"><a class="page-link" href="#n">Next <i
                            class="fas fa-arrow-circle-right"></i></a></li>
            </ul>
        </div>

    </div>
    <button id="upbtn" class="btn btn-default"><i class="fas fa-chevron-circle-up"></i></button>
</body>

</html>
<script>
$("#upbtn").hide();
const baseUrl = $("#baseurl").val();
const url = baseUrl + "/source.php";
const searchBtn = $("#searchbtn");
const newsResults = $("#newsresults");
const defaultImage = baseUrl + "/default.jpg";
const spinner = "<div class='spinner-grow text-primary'></div>"

//NEWS CLASSS
class News {
    newsData = [];

    newsResAppend = (id, title, imgSrc, desc, more, date) => {
        let val = " <li role='button' data-item=" + id +
            " class='shadow p-4 mb-4 bg-white list-group-item newsressub'>" +
            "<span class='mb-1 text-left text-dark publishtime'>" + date + "</span>" +
            "<h3 class='text-info'>" + title +
            "</h3><hr class='w-100 bg-info'>" +
            "<div class='imgcont row '><img class='newsresimg w-100 col-12 col-md-6' src=" +
            imgSrc + ">" +
            "<div class='col-md-6 col-12 w-100 ml-1'>" + desc + "<br><a href=" + more +
            ">More Details</a></div></div></li>";
        newsResults.append(val);
    }
    newsAll = (category = "", searchquery = "") => {
        this.newsData = [];
        var newData = new FormData();
        if (searchquery != "") {
            newData.append("searchquery", searchquery);
            $(".topic").removeClass("active");
        } else if (category != "") {
            newData.append("category", category);
        } else {
            newData.append("allnews", true);
            $(".topic").first().addClass("active");
        }
        let beforeSend = () => {
            newsResults.html("<h3 class='text-center mt20'>" + spinner + "</h3>");
            $(".pagination").hide();
        }
        let success = (data) => {
            newsResults.html("");
            let resData = JSON.parse(data);
            if (Array.isArray(resData.articles) || Array.isArray(resData.sources) && resData.status == "ok") {
                let loop = 10;
                if (Array.isArray(resData.articles)) {
                    if (resData.articles.length < 10) {
                        loop = resData.articles.length;
                    }
                    this.newsData = resData.articles;
                }
                for (let i = 0; i < loop; i++) {
                    if (Array.isArray(resData.articles)) {
                        this.articles(resData.articles[i], i);
                    }
                }
                this.pageOps(this.newsData);
            } else {
                newsResults.html("<h3 class='text-danger text-center mt-5'>No Results</h3>");
            }
        }
        $.ajax({
            url: url,
            type: "POST",
            data: newData,
            contentType: false,
            processData: false,
            beforeSend: beforeSend,
            success: success
        });
    }
    articles = (article, i) => {
        let title = article.title;
        let imgSrc = article.urlToImage;
        let more = article.url;

        let date = article.publishedAt;
        date = date.replace("T", " | ");
        date = date.split(".")[0];
        if (imgSrc == null) {
            imgSrc = defaultImage;
        }
        let desc = article.description;
        this.newsResAppend(i, title, imgSrc, desc, more, date);
    }
    pageOps = (resourceType) => {
        if (resourceType.length > 10) {
            $(".prev").hide();
            $(".next").show();
            $(".next").attr("data-pg", 10);
            $(".prev").attr("data-pg", 0);
            $(".pagination").show();
        } else {
            $(".pagination").hide();
        }
    }
    pagination = (index) => {
        newsResults.html("");
        let ind = parseInt(index);
        let loopInd;
        if (this.newsData.length - ind < 10) {
            loopInd = this.newsData.length;
        } else {
            loopInd = ind + 10;
        }
        for (let i = ind; i < loopInd; i++) {
            let title;
            try {
                title = this.newsData[i].title;
            } catch (err) {
                title = false;
            }
            if (title) {
                title = this.newsData[i].title;
                let imgSrc = this.newsData[i].urlToImage;
                let more = this.newsData[i].url;
                let date = this.newsData[i].publishedAt;
                date = date.replace("T", " | ");
                date = date.split(".")[0];
                if (imgSrc == null) {
                    imgSrc = defaultImage;
                }
                let desc = this.newsData[i].description;
                this.newsResAppend(i, title, imgSrc, desc, more, date);
            }
        }
        if (loopInd % 10 == 0 && loopInd != this.newsData.length) {
            $(".next").attr("data-pg", ind + 10);
            $(".next").show();
            if (loopInd > 10) {
                $(".prev").attr("data-pg", ind - 10);
                $(".prev").show();
            } else {
                $(".prev").hide();
            }
        } else {
            $(".prev").attr("data-pg", ind - 10);
            $(".prev").show();
            $(".next").hide();
        }
    }
}

//INTIALIZING NEW NEWS
let newNews = new News();
newNews.newsAll();

//SEARCH BASED ON CATEGORY
$(".topic").click(function() {
    $("#searchbar").val("");
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    this.cat = $(this).attr("data-type");
    newNews.newsAll(this.cat, "");
});

//ON SCROLL SHOW THE UP BUTTON
$(document).scroll(function() {
    if ($(this).scrollTop() > 30) {
        $("#navmy").addClass("fixed-top");
        $("#upbtn").fadeIn(500);
    } else {
        $("#navmy").removeClass("fixed-top");
        $("#upbtn").fadeOut(500);
    }
});

//GO TO TOP
$("#upbtn").click(function() {
    $(document).scrollTop(0);
});

//NEXT AND PREVIOUS NEWS LOAD
$(".next").on("click", function() {
    this.val = $(this).attr("data-pg");
    console.log(this.val);
    newNews.pagination(this.val);
    $(document).scrollTop(0);
});
$(".prev").click(function() {
    this.val = $(this).attr("data-pg");
    console.log(this.val);
    newNews.pagination(this.val);
    $(document).scrollTop(0);
});

//SEARCHING QUERY IN SEARCH BAR
searchBtn.click(function() {
    this.query = $("#searchbar").val();
    newNews.newsAll("", this.query);
    $("#searchbar").val("");
});
</script>