<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <style>
        /* Reset and Centering */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow: hidden;
        }

        .coming-soon-wrapper {
            position: relative;
            /* Padding 20px top/bottom, 40px left/right */
            padding: 20px 40px;
            display: inline-block;
            box-sizing: border-box;
        }

        .text {
            color: #000000;
            font-size: 2.5rem;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin: 0;

            opacity: 0;
            transform: translateY(20px);

            animation: fadeInUp 2s cubic-bezier(0.23, 1, 0.32, 1) forwards;
            animation-delay: 0.8s;
        }

        /* SVG Border Styling */
        .border-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .border-rect {
            fill: none;
            stroke: #000000;
            stroke-width: 2px;
            /* pathLength="100" allows us to use 0-100 for dasharray regardless of actual pixel size */
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawBorder 2s cubic-bezier(0.3, 0, 0.1, 1) forwards;
        }

        /* Animations */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes drawBorder {
            to {
                stroke-dashoffset: 0;
            }
        }



        @media screen and (max-width: 768px) {

            .coming-soon-wrapper {
                max-width: 90%;
            }

        }
    </style>
</head>

<body>
    <div class="coming-soon-wrapper">
        <h1 class="text">Coming Soon</h1>
        <svg class="border-svg">
            <rect class="border-rect" x="0.5" y="0.5" width="calc(100% - 1px)" height="calc(100% - 1px)"
                pathLength="100" />
        </svg>
    </div>
</body>

</html>