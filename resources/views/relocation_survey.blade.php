<!DOCTYPE html>
<html>
<head>
    <title>Relocation Survey</title>
    <style>
        .header{
            width: 100%;
            text-align: center;
        }
        .logo{
            height: 230px;
            width: auto;
            margin: 0 auto;
        }
        .single-line-h2{
            text-align: center;
            font-style: italic;
            font-family: Calibri, sans-serif;
            font-size: 22pt;
            white-space: nowrap; 
            overflow: visible;
        }
        .from{
            font-family: 'Times New Roman';
            font-size: 12pt;
        }
        .align-name{
            margin-top: 17px;
            font-family: 'Times New Roman';
            font-size: 12pt;
            font-weight: bold
        }
        .rounded{
            border: 1px solid black; /* Blue border */
            border-radius: 15px; /* Rounded corners */
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-family: Calibri, sans-serif;
            font-size: 16pt;
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .textsFont{
            font-family: 'Times New Roman';
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/public/images/logo.png" alt="logo" class="logo">
    </div>
    <hr style="color: green">
    <h2 class="single-line-h2">
        <span style="color: blue">GEOPETE</span>
        <span style="color: green">GEODETIC</span>
        <span style="color: blue">SURVEYING</span>
        <span style="color: green">SERVICES</span>
    </h2>
    <hr style="color:blue">

    <div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- FROM Column -->
                <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                    <p style="margin-bottom: 5px;"><b>FROM:</b></p>
                    <p class="from"><b>Peter June Alaon</b></p>
                    <p class="from" style="line-height: 3px;">Geodetic Engineer- GGSS Proprietor</p>
                    <p class="from" style="line-height: 3px;">B8 L29, Elliston Place, Pasong Camachile II,</p>
                    <p class="from" style="line-height: 3px;">General Trias, Cavite, 4107</p>
                    <p class="from" style="line-height: 3px; color: blue;"><a href="mailto:geopetesurv@gmail.com"></a>geopetesurv@gmail.com</p>
                    <p class="from" style="line-height: 30px;">Mobile Nos.: 0919-0777886 (Smart) / </p>
                    <p class="from" style="line-height: 3px;">0915-5985620(Globe)</p>
                </td>
                
                <!-- Vertical Border Column -->
                {{-- <td style="width: 1px; background-color: black; padding: 0;"></td> --}}
                
                <!-- ATTENTION TO Column -->
                <td style="width: 49%; vertical-align: top; padding-left: 15px;">
                    <p style="margin-bottom: 5px;"><b>ATTENTION TO:</b></p>
                    <div class="align-name">
                        {{ $clientName ?? 'Clients Name' }}
                    </div>
                </td>
            </tr>
        </table>

        <h2 class="rounded">DELIVERABLES</h2>
        <p>Relocation Survey: </p>
        <ul class="textsFont" type="square">
            <li>(1) set each of Original Prints and Signed by Geodetic Engineer.</li>
            <li>Plans: Two (2) sets of Blue Print, signed and sealed.</li>
            <li>Reports and Evaluations: Two (2) sets. A4 size.</li>
            <li>Photocopies of PRC ID’s and PTR, signed and sealed.</li>
            <li>Drawing: 3-dimensional CAD format map.</li>
        </ul>

        <h2 class="rounded">WORK SCHEDULE</h2>
        <p>Relocation Survey: </p>
        <ul class="textsFont" type="square">
            <li>(1) set each of Original Prints and Signed by Geodetic Engineer.</li>
            <li>Plans: Two (2) sets of Blue Print, signed and sealed.</li>
            <li>Reports and Evaluations: Two (2) sets. A4 size.</li>
            <li>Photocopies of PRC ID’s and PTR, signed and sealed.</li>
            <li>Drawing: 3-dimensional CAD format map.</li>
        </ul>
    </div>
</body>
</html>
