<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .reports-content {
            flex: 1;
            background: #296218;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            border-radius: 15px;
            min-height: calc(80vh - 80px); /* Adjust based on your header height */
        }

        .reports-title {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .reports-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 1135px;
        }

        .report-button {
            background-color: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            padding: 20px 25px;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: left;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
        }

        .report-button:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.3);
            color: #2c3e50;
        }

        .report-button:active {
            transform: translateY(0);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .reports-content {
                padding: 20px;
            }
            
            .reports-title {
                font-size: 24px;
            }
            
            .report-button {
                padding: 15px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <!-- Reports Content -->
            <div class="reports-content">
                <h1 class="reports-title">Reports</h1>
                
                <div class="reports-grid">
                    <a href="{{ route('client.report.rpci') }}" class="report-button">
                        RPCI (Report on the Physical Count of Inventory)
                    </a>
                    
                    <a href="{{ route('client.report.rsmi') }}" class="report-button">
                        RSMI (Report of Supplies and Materials Issued)
                    </a>
                    
                    <a href="{{ route('client.report.ppes') }}" class="report-button">
                        PPES (Property Plant and Equipment)
                    </a>
                    
                    <a href="{{ route('client.report.rpc-ppe') }}" class="report-button">
                        RPC PPE (Report on the property plant and Equipment)
                    </a>
                </div>
            </div>
        </div>   
    </div>

    <script>
        // Add any additional JavaScript functionality here
        $(document).ready(function() {
            // Add loading states or animations if needed
            $('.report-button').on('click', function() {
                $(this).css('opacity', '0.7');
            });
        });
    </script>
</body>
</html>