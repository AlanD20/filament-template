<header class="mb-4 flex flex-col gap-2">
    <div class="flex h-2 w-full">
        <div
            class="!print-color-exact h-full w-1/2 !bg-[#395623]"
            style="background-color: rgb(57 86 35) !important;-webkit-print-color-adjust: exact !important;print-color-adjust: exact !important; border-bottom: 16px solid rgb(57 86 35);"
        >
        </div>
        <div
            class="!print-color-exact h-full w-1/2 !bg-[#757070]"
            style="background-color: rgb(117 112 112) !important;-webkit-print-color-adjust: exact !important;print-color-adjust: exact !important; border-bottom: 16px solid rgb(117 112 112);"
        >
        </div>
    </div>

    <div class="container-content flex items-center gap-4 rtl:justify-between">

        {{-- <img
            class="w-32 h-12"
            src="{{ asset('/images/dark-logo.svg') }}"
            alt="logo"
        > --}}

        <div class="content flex flex-col gap-1 justify-self-center rtl:text-right">
            <span class="mt-4 text-2xl font-semibold uppercase">
                {{ __('reports.header.heading') }}
            </span>
            <span
                class="block text-justify uppercase"
                style=" width: 374px !important;"
            >
                {{ __('reports.header.subheading') }}
            </span>
        </div>
    </div>
</header>
