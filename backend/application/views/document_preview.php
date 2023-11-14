<script>
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        let auth = JSON.parse(this.response)
        userId = auth['id']
        roleName = auth['role_name']
        departmentId = auth['department_id']
        companyId = auth['company_id']
    }
    xhttp.open("POST", "/edocs/backend/access/check", false);
    xhttp.send();
</script>
<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>edocs - Preview document</title>
    <link rel="icon" href="<?php echo base_url(); ?>static/icon-brand.png" type="image/x-icon">
    <!-- CSS files -->
    <link href="<?php echo base_url(); ?>dist/css/tabler.min.css?1684106062" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>dist/css/tabler-flags.min.css?1684106062" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>dist/css/tabler-payments.min.css?1684106062" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>dist/css/demo.min.css?1684106062" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf_viewer.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>dist/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css">

    <style>
        /*@import url('https://rsms.me/inter/inter.css');*/

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }

        #pdf_container {
            background: #ccc;
            text-align: center;
            display: none;
            padding: 5px;
            max-height: 820px;
            overflow: auto;
        }

        .back {
            position: absolute;
            left: 0;
            top: 15px;
        }

        .zoom-in-out {
            position: absolute;
            top: 8px;
            left: 50%;
            width: fit-content;
            transform: translateX(-50%);
            opacity: 80%;
        }

        .zoom-in-out>button:first-child {
            margin-right: 1.5px;
            /* border-right: #ccc; */
        }

        .btn.btn-link:hover {
            background-color: transparent !important;
        }

        .btn.btn-link {
            --tblr-btn-active-border-color: transparent !important
        }

        .datepicker {
            padding: .4375rem .75rem;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 9999;
        }

        #loading img {
            position: absolute;
            left: 50%;
            top: 50vh;
            transform: translate(-50%, -50%);
            width: 40px;
        }
    </style>
</head>

<body class=" d-flex flex-column">
    <script src="<?php echo base_url(); ?>dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page page-center">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
            <div class="container-xl">
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <button onclick="navigateBack()" class="btn btn-link p-0">
                        <img src="<?php echo base_url(); ?>static/arrow-narrow-left.svg" height="32" style="object-fit: contain;" alt="Tabler">
                    </button>
                </h1>

            </div>
        </header>
        <div class="page-wrapper">

            <!-- <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img src="<?php echo base_url(); ?>static/logo-dark.svg" width="90" height="38" alt="" style="object-fit: cover;"></a>

            </div> -->
            <!-- <div class="back">
                <button onclick="history.back()" class="btn btn-link btn-lg w-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l14 0"></path>
                        <path d="M5 12l4 4"></path>
                        <path d="M5 12l4 -4"></path>
                    </svg> Back
                </button>
            </div> -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <!-- Page pre-title -->

                            <h2 class="page-title">
                                Document preview
                            </h2>
                        </div>
                        <!-- Page title actions -->
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <div class="al-user1 d-none">
                                    <span class="d-none d-sm-inline">
                                        <a href="#" class="btn btn-danger" data-id="<?php echo explode('/', uri_string())[count(explode('/', uri_string())) - 2]; ?>" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                            Delete
                                        </a>
                                    </span>
                                    <span class="d-sm-none">
                                        <a href="#" class="btn btn-danger btn-icon" data-id="<?php echo explode('/', uri_string())[count(explode('/', uri_string())) - 2]; ?>" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 7l16 0"></path>
                                                <path d="M10 11l0 6"></path>
                                                <path d="M14 11l0 6"></path>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                            </svg>
                                        </a>
                                    </span>
                                </div>

                                <div class="al-user2 d-none">
                                    <span class="d-none d-sm-inline">
                                        <a href="#" class="btn" data-id="<?php echo explode('/', uri_string())[count(explode('/', uri_string())) - 2]; ?>" data-bs-toggle="modal" data-bs-target="#edit_modal">
                                            Edit
                                        </a>
                                    </span>
                                    <span class="d-sm-none">
                                        <a href="#" class="btn btn-icon" data-id="<?php echo explode('/', uri_string())[count(explode('/', uri_string())) - 2]; ?>" data-bs-toggle="modal" data-bs-target="#edit_modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                <path d="M16 5l3 3"></path>
                                            </svg>
                                        </a>
                                    </span>
                                </div>


                                <form action="download" method="post" class="al-user4 d-none form-download">
                                    <button type="submit" class="btn btn-primary d-none d-sm-inline-block">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M7 11l5 5l5 -5"></path>
                                            <path d="M12 4l0 12"></path>
                                        </svg>

                                        Download
                                    </button>
                                    <button type="submit" class="btn btn-primary d-sm-none btn-icon" aria-label="Download">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M7 11l5 5l5 -5"></path>
                                            <path d="M12 4l0 12"></path>
                                        </svg>

                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-cards">
                        <div class="col-12">
                            <form action="https://httpbin.org/post" method="post" class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Document details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                <div class="col-md-6 col-xl-12">


                                                    <div class="mb-3 row">
                                                        <label class="col-3 col-form-label">Document Code</label>
                                                        <div class="col">
                                                            <input type="text" class="form-control-plaintext" value="<?php echo $doc_code; ?>" required>

                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-3 col-form-label">Document Name</label>
                                                        <div class="col">

                                                            <input type="text" class="form-control-plaintext" value="<?php echo $doc_name; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <div class="col-3 col-form-label">Update File</div>
                                                        <div class="col">

                                                            <input type="text" class="form-control-plaintext" value="<?php echo $doc_file; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-3 col-form-label">Document Exp. Date</label>
                                                        <div class="col">

                                                            <input type="text" class="form-control-plaintext" value="<?php echo $valid_until; ?>" required>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </form>
                        </div>






                    </div>
                    <div class="row row-deck row-cards position-relative" style="padding-left: 8px;padding-right:8px">
                        <!-- <input type="button" id="btnPreview" value="Preview PDF Document" onclick="LoadPdfFromUrl('PDFs/Sample.pdf')" /> -->
                        <!-- <hr /> -->
                        <div id="pdf_container">
                        </div>
                        <div class="btn-group zoom-in-out" role="group" aria-label="Basic example">
                            <button id="zoom_in_button" class="btn btn-secondary w-100 btn-icon" aria-label="Zoom in">
                                <!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-zoom-in" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M7 10l6 0"></path>
                                    <path d="M10 7l0 6"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                            </button>
                            <button id="zoom_out_button" class="btn btn-secondary w-100 btn-icon" aria-label="Zoom out">
                                <!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-zoom-out" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M7 10l6 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                            </button>
                        </div>


                    </div>
                </div>
            </div>


        </div>
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright &copy; 2023
                                <a href="." class="link-secondary">J2 SDI Team</a>.
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-pink icon-filled icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <!-- All rights reserved. -->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div class="modal modal-blur fade" id="edit_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formEdit">
                    <input type="hidden" name="sli_edocs_<?php echo $index;?>_id" id="edit_sli_edocs_<?php echo $index;?>_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Document Code</label>
                            <input type="text" class="form-control" name="sli_edocs_<?php echo $index;?>_code" id="edit_sli_edocs_<?php echo $index;?>_code" placeholder="Document code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Document Name</label>
                            <input type="text" class="form-control" name="sli_edocs_<?php echo $index;?>_name" id="edit_sli_edocs_<?php echo $index;?>_name" placeholder="Document name" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-label">Update File</div>
                            <input type="file" name="sli_edocs_<?php echo $index;?>_file" id="edit_sli_edocs_<?php echo $index;?>_file" class="form-control" accept=".doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf">
                            <small class="form-hint"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Document Exp. Date</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-icon mb-2">
                                        <input class="form-control datepicker" placeholder="Document Exp. Date" name="sli_edocs_<?php echo $index;?>_valid_until_date" id="edit_sli_edocs_<?php echo $index;?>_valid_until_date" data-date-format="yyyy-mm-dd" required>
                                        <span class="input-icon-addon"><!-- Download SVG icon from https://tabler-icons.io/i/calendar -->
                                            <svg  class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                                <path d="M16 3v4"></path>
                                                <path d="M8 3v4"></path>
                                                <path d="M4 11h16"></path>
                                                <path d="M11 15h1"></path>
                                                <path d="M12 15v3"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-check">
                                        <input class="form-check-input" name="togg_no_exp" type="checkbox">
                                        <span class="form-check-label">No Expiration Date</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-check">
                                        <input class="form-check-input check-all" type="checkbox">
                                        <span class="form-check-label">CHECK ALL</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row departments">
                            <!-- ajax call -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <!-- Download SVG icon from https://tabler-icons.io/i/plus -->
                            <svg  class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg> Edit document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="" id="formDeleteConfirm">
                        <input type="hidden" name="id" id="delete_id">
                    </form>
                    <div class="modal-title">Delete document</div>
                    <div>Are you sure you want to delete this document?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="formDeleteConfirm" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div id="loading" style="display:none"><img src="<?php echo base_url(); ?>static/loading.gif" alt=""></div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="<?php echo base_url(); ?>dist/js/tabler.min.js?1684106062" defer></script>
    <script src="<?php echo base_url(); ?>dist/js/demo.min.js?1684106062" defer></script>
    <script src="<?php echo base_url(); ?>dist/js/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
    <script src="<?php echo base_url(); ?>dist/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';
        var pdfDoc = null;
        var scale = 1; //Set Scale for zooming PDF.
        var resolution = 2; //Set Resolution to Adjust PDF clarity.

        //     LoadPdfFromUrl(convertDataURIToBinary("<?php echo $uri; ?>"))
        LoadPdfFromUrl("<?php echo $uri; ?>")

        function LoadPdfFromUrl(url) {
            //Read PDF from URL.
            pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;

                //Reference the Container DIV.
                var pdf_container = document.getElementById("pdf_container");
                pdf_container.style.display = "block";

                //Loop and render all pages.
                for (var i = 1; i <= pdfDoc.numPages; i++) {
                    RenderPage(pdf_container, i);
                }
            });
        };

        function RenderPage(pdf_container, num) {
            pdfDoc.getPage(num).then(function(page) {
                //Create Canvas element and append to the Container DIV.
                var canvas = document.createElement('canvas');
                canvas.id = 'pdf-' + num;
                ctx = canvas.getContext('2d');
                pdf_container.appendChild(canvas);

                //Create and add empty DIV to add SPACE between pages.
                var spacer = document.createElement("div");
                spacer.style.height = "20px";
                pdf_container.appendChild(spacer);

                // console.log(scale)
                //Set the Canvas dimensions using ViewPort and Scale.
                var viewport = page.getViewport({
                    scale: scale
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                //Render the PDF page.
                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                    // transform: [resolution, 0, 0, resolution, 0, 0]
                };

                page.render(renderContext);
            });
        };

        document.addEventListener("contextmenu", (event) => {
            event.preventDefault();
        });

        $(document).ready(function() {
            // roleName = 'USER 5'
            switch (roleName) {
                case 'ADMIN':
                case 'USER 1':
                    $('.al-user1').removeClass('d-none')
                    $('.al-user2').removeClass('d-none')
                    $('.al-user4').removeClass('d-none')
                    break;
                case 'USER 2':
                    $('.al-user2').removeClass('d-none')
                    $('.al-user4').removeClass('d-none')
                    break;
                case 'USER 3':
                case 'USER 4':
                    // code block
                    $('.al-user4').removeClass('d-none')
                    break;
            }

            $('#zoom_in_button').click(function() {
                scale = scale + 0.25;
                //Reference the Container DIV.
                var pdf_container = document.getElementById("pdf_container");
                pdf_container.style.display = "block";
                pdf_container.innerHTML = ''

                //Loop and render all pages.
                for (var i = 1; i <= pdfDoc.numPages; i++) {
                    RenderPage(pdf_container, i);
                }
            })
            $('#zoom_out_button').click(function() {
                if (scale > 0.25) {
                    scale = scale - 0.25;
                    //Reference the Container DIV.
                    var pdf_container = document.getElementById("pdf_container");
                    pdf_container.style.display = "block";
                    pdf_container.innerHTML = ''

                    //Loop and render all pages.
                    for (var i = 1; i <= pdfDoc.numPages; i++) {
                        RenderPage(pdf_container, i);
                    }
                }
            })

            loadDepartment()
            $('.datepicker').datepicker()

            $(document).on('submit', '.form-download', function(e) {
                e.preventDefault()
                $("<input />").attr("type", "hidden")
                    .attr("name", "user_id")
                    .attr("value", userId)
                    .appendTo(this);
                this.submit()
                $('input', this).remove()
            })

            //edit modal
            $(document).on('show.bs.modal', '#edit_modal', function(event) {
                const button = $(event.relatedTarget) // Button that triggered the modal
                const id = button.data('id') // Extract info from data-* attributes

                if (id === undefined) {
                    return;
                }

                const context = this
                $.ajax({
                        method: "GET",
                        url: "<?php echo base_url(); ?>backend/<?php echo str_replace("_","-",$index);?>/" + id
                    })
                    .done(function(res) {
                        // console.log(res)
                        $('#formEdit')[0].reset();
                        $('#formEdit input').removeClass('updated')

                        $('#edit_sli_edocs_<?php echo $index;?>_id').val(res['sli_edocs_<?php echo $index;?>_id'])
                        $('#edit_sli_edocs_<?php echo $index;?>_code').val(res['sli_edocs_<?php echo $index;?>_code'])
                            .attr('data-old', res['sli_edocs_<?php echo $index;?>_code'])
                        $('#edit_sli_edocs_<?php echo $index;?>_name').val(res['sli_edocs_<?php echo $index;?>_name'])
                            .attr('data-old', res['sli_edocs_<?php echo $index;?>_name'])
                        $('#edit_sli_edocs_<?php echo $index;?>_file').prop('required', false).siblings('small').text(res['sli_edocs_<?php echo $index;?>_file'])

                        $('#edit_sli_edocs_<?php echo $index;?>_valid_until_date').val(res['sli_edocs_<?php echo $index;?>_valid_until_date'])
                            .attr('data-old', res['sli_edocs_<?php echo $index;?>_valid_until_date'])
                        if (res['sli_edocs_<?php echo $index;?>_valid_until_date'] == '9999-12-31') {
                            $('#edit_sli_edocs_<?php echo $index;?>_valid_until_date').prop('disabled', true)
                            $('input[name=togg_no_exp]', context).prop('checked', true)
                        }

                        res.departments.forEach(function(item, index) {
                            if (res['sli_edocs_<?php echo $index;?>_dept_owner'] == item['sli_edocs_department_id']) {
                                $('.departments input[data-id="' + item['sli_edocs_department_id'] + '"]', context).prop('checked', true).addClass('old').prop('disabled', true)
                            } else {
                                $('.departments input[data-id="' + item['sli_edocs_department_id'] + '"]', context).prop('checked', true).addClass('old')
                            }
                        })

                        if (res.departments.length == $('.departments input', context).length) {
                            $('.check-all', context).prop('checked', true)
                        }

                        // initCheckboxValidation('#formEdit')
                    })
            })
            $(document).on('change', '#formEdit input:not([type="checkbox"])', function() {
                if ($(this).val() == $(this).attr('data-old')) {
                    $(this).removeClass('updated');
                } else {
                    $(this).addClass('updated');
                }

                if ($(this).attr('name') == 'sli_edocs_<?php echo $index;?>_file') {
                    if ($(this).val()) {
                        const segs = $(this).val().split("\\")
                        const val = segs[segs.length - 1]
                        $('.form-hint').text(val)

                    } else {
                        $('.form-hint').text('')
                        $(this).prop('required', true)
                    }

                }
            })
            $(document).on('change', '.check-all', function() {
                const context = this;
                if ($(context).prop('checked')) {
                    $('.departments input', $(context).parents('.modal-body')).each(function() {
                        if ($(this).prop('checked') != $(context).prop('checked')) {
                            $(this).click();
                        }
                    })
                } else {
                    $('.departments input', $(context).parents('.modal-body')).each(function() {
                        if ($(this).prop('checked') != $(context).prop('checked')) {
                            $(this).click();
                        }
                    })
                }
            })
            $(document).on('change', '#formEdit input[type="checkbox"]:not(.check-all,[name="togg_no_exp"])', function() {
                if (($(this).hasClass('old') && $(this).prop('checked')) ||
                    (!$(this).hasClass('old') && !$(this).prop('checked'))) {
                    $(this).removeClass('updated');
                } else {
                    $(this).addClass('updated');
                }
            })
            $(document).on('submit', '#formEdit', function(e) {
                e.preventDefault()

                const id = $('input[name="sli_edocs_<?php echo $index;?>_id"]', this).val()

                let data = new FormData();
                $.each($('.updated', this).serializeArray(), function(i, field) {
                    if (!['sli_edocs_<?php echo $index;?>_file', 'sli_edocs_<?php echo $index;?>_valid_until_date', 'togg_no_exp', 'sli_edocs_<?php echo $index;?>_id', 'sli_edocs_department_id[]'].includes(field.name)) {
                        data.append(field.name.replace('edit_', ''), field.value);
                    }
                });

                let toDelete = []
                let toAdd = []
                $.each($('input[type="checkbox"]:not(.check-all,[name="togg_no_exp"]).updated', this), function(i, field) {
                    if ($(this).hasClass('old')) {
                        toDelete.push(field.value)
                    } else {
                        toAdd.push(field.value)
                    }
                })
                if (toDelete.length > 0) {
                    data.append('deleted_dept', JSON.stringify(toDelete))
                }
                if (toAdd.length > 0) {
                    data.append('added_dept', JSON.stringify(toAdd))
                }

                if ($('#edit_sli_edocs_<?php echo $index;?>_file.updated').length > 0) {
                    data.append('file', $('#edit_sli_edocs_<?php echo $index;?>_file.updated')[0].files[0]);
                }

                if ($('#edit_sli_edocs_<?php echo $index;?>_valid_until_date.updated').length > 0) {
                    data.append('sli_edocs_<?php echo $index;?>_valid_until_date', $('#edit_sli_edocs_<?php echo $index;?>_valid_until_date.updated').val());
                }

                if (data.entries().next().done) {
                    return
                }
                data.append('user_id', userId);

                // Display the values
                // for (const value of data.values()) {
                //     console.log(value);
                // }

                $.ajax({
                    url: "<?php echo base_url(); ?>backend/<?php echo str_replace("_","-",$index);?>/" + id,
                    data: data,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    complete: function() {
                        $("#loading").hide();
                    },
                    error: function(res) {
                        console.log(res)
                    },
                    success: function(res) {
                        // console.log(res)
                        alert(res.message)
                        location.reload()
                    },
                    type: 'POST'
                });
            })
            $(document).on('change', 'input[name="togg_no_exp"]', function() {
                if ($(this).prop('checked') == true) {
                    $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').val('9999-12-31')
                    if ($(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').attr('data-old') != '9999-12-31') {
                        $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').addClass('updated')
                    } else {
                        $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').removeClass('updated')
                    }
                    $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').prop('disabled', true)
                } else {
                    $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').prop('disabled', false)
                    $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').val('')
                        // $(this).parents('.row').find('input[name=sli_edocs_<?php echo $index;?>_valid_until_date]').prop('required', true)
                }
            })
            $(document).on('change', 'input[name="sli_edocs_<?php echo $index;?>_valid_until_date"]', function() {
                if ($(this).val() == '9999-12-31') {
                    $(this).prop('disabled', true)
                    $(this).parents('.row').find('input[name=togg_no_exp]').prop('checked', true)
                }
            })


            //delete modal
            $(document).on('show.bs.modal', '#delete_modal', function(event) {
                const button = $(event.relatedTarget) // Button that triggered the modal
                const id = button.data('id') // Extract info from data-* attributes
                $('#delete_id').val(id)
            })
            $(document).on('submit', '#formDeleteConfirm', function(e) {
                e.preventDefault()

                const id = $('input[name="id"]', this).val()
                $.ajax({
                        method: "DELETE",
                        url: "<?php echo base_url(); ?>backend/<?php echo str_replace("_","-",$index);?>/" + id + "?user_id=" + userId,
                        beforeSend: function() {
                            $('#loading').show();
                        }
                    })
                    .done(function(res) {
                        // console.log(res)
                        // alert(res['message'])
                        navigateBack()
                    })
                    .fail(function(res) {
                        console.log(res)
                    })
                    .always(function() {
                        $('#loading').hide();
                    });
            })

        })

        function convertDataURIToBinary(dataURI) {
            var BASE64_MARKER = ';base64,';
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for (i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        }

        function initCheckboxValidation(selector) {
            const form = document.querySelector(selector);
            const checkboxes = form.querySelectorAll('input[name="sli_edocs_department_id[]"]');
            const checkboxLength = checkboxes.length;
            const firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;
            const secondCheckbox = checkboxes[1]

            function init() {
                if (firstCheckbox) {
                    for (let i = 0; i < checkboxLength; i++) {
                        checkboxes[i].addEventListener('change', checkValidity);
                    }

                    checkValidity();
                }
            }

            function isChecked() {
                for (let i = 0; i < checkboxLength; i++) {
                    if (checkboxes[i].checked) return true;
                }

                return false;
            }

            function checkValidity() {
                const errorMessage = !isChecked() ? 'At least one checkbox must be selected.' : '';
                firstCheckbox.setCustomValidity(errorMessage);
            }

            init();
        }

        function loadDepartment() {
            $.ajax({
                    method: "GET",
                    url: "<?php echo base_url(); ?>backend/department?sli_edocs_company_id=" + companyId
                })
                .done(function(res) {
                    // console.log(res)

                    res.forEach(function(item, index) {
                        if (item['sli_edocs_department_id'] == departmentId) {
                            var html = '<div class="col-lg-4">';
                            html += '<div class="mb-3">';
                            html += '<label class="form-check">';
                            html += '<input class="form-check-input" name="sli_edocs_department_id[]" data-id="' + item['sli_edocs_department_id'] + '" value="' + item['sli_edocs_department_id'] + '" type="checkbox" checked disabled>';
                            html += '<span class="form-check-label">' + item['sli_edocs_department_name'] + '</span>';
                            html += '</label>';
                            html += '</div>';
                            html += '</div>';
                        } else {
                            var html = '<div class="col-lg-4">';
                            html += '<div class="mb-3">';
                            html += '<label class="form-check">';
                            html += '<input class="form-check-input" name="sli_edocs_department_id[]" data-id="' + item['sli_edocs_department_id'] + '" value="' + item['sli_edocs_department_id'] + '" type="checkbox">';
                            html += '<span class="form-check-label">' + item['sli_edocs_department_name'] + '</span>';
                            html += '</label>';
                            html += '</div>';
                            html += '</div>';
                        }

                        $('.departments').append(html)
                    })
                })
        }


        function navigateBack() {
            try {
                var stacks = JSON.parse(sessionStorage.getItem('previousPage'));
                var previousPage = stacks[stacks.length - 1]

                stacks.pop()
                sessionStorage.setItem('previousPage', JSON.stringify(stacks));
                window.location.href = previousPage
            } catch (error) {
                window.close()
            }

        }
    </script>
</body>

</html>