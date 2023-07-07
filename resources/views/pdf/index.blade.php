<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PDF</title>
    <style>
        .container {
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .header {
            width: 100%;
            display: flex;
            align-items: start;
            flex-direction: column;
            line-height: 1.5em
        }
        .mt-12 {
            margin-top: 6rem !important;
        }
        .mt-5 {
            margin-top: 3rem !important;
        }
       /*  .title {
            font-weight: 'bold',
            font-size: 21px,
            text-decoration: 'underline'
        } */
        .dating {
            display: flex;
            align-items: end;
            flex-direction: row;
            justify-content: center;
        }
        .signature_base64 {
           text-align: right
        }
        
    </style>
</head>
<body>
    <div class="container">
       
        <p style="text-align: right">{{ $extension->created_at->isoFormat('LL') }}</p>
        
        <div class="header">
            <p>Groupe scolaire: {{ $user->accounts[0]->name }} </p>
            <p>Autorisation: {{ $user->accounts[0]->immatriculation }}</p>
            <p>Téléphone: {{ $user->phone }}</p>
        </div>

        <div class="mt-12 sub-header">
            <span style="font-weight: bold; text-decoration: underline; text-transform: uppercase">objet:</span>
            <span>Moratoire frais de scolarité</span>
            @if(!$extension->status )
                <span style="color:red">(Attention, ce moratoire n'est plus valide!!)</span>
            @endif    
        </div>

        <div class="mt-5">
            <p>Monsieur {{ $extension->inscription->student->father_name ? $extension->inscription->student->father_name : $extension->inscription->student->mother_name  }} </p>
            <p>
                Suite à votre demande, nous vous accordons momentatement un delai pour le payement des frais de scolarité de {{ $extension->inscription->student->fname }} {{ $extension->inscription->student->lname }} éléve en classe de {{ $extension->inscription->classroom->name }} valade jusqu'au {{ \Carbon\carbon::parse($extension->valid_until_at)->format('d-m-y') }}
            </p>
            <p>Nous comptons sur votre bonne fois habituel et merci de continuer à nous faire confiance pour l'education de votre enfant.</p>
            <p>La direction,</p>
        </div>

        <div class="mt-5 signature_base64">
            <img src="{{$signature_base64}}" />
        </div>

    </div>
   
</body>
</html>