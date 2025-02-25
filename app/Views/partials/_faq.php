<div x-data="{ openFAQ: null }" class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg border rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">Frequently Asked Questions</h2>

    <div class="divide-y divide-gray-200">
        <template x-for="(faq, index) in [
            { question: 'How do I upload a file?', answer: 'Click on the upload area or drag and drop your files into the designated space.' },
            { question: 'What file formats are supported?', answer: 'We support a wide range of formats. <a href=\'supported-files\' class=\'text-blue-600 hover:text-blue-800\'>Supported files</a>.' },
            { question: 'How long does the conversion take?', answer: 'Conversion times depend on the file size and format but typically take a few seconds to a minute.' },
            { question: 'Is there a file size limit?', answer: 'Yes, the maximum file size is 200MB per file.' },
            { question: 'How can I download my converted files?', answer: 'Once the conversion is complete, you can click the download button next to your file.' },
            { question: 'Can I convert multiple files at once?', answer: 'Yes, you can upload multiple files and convert them simultaneously.' }
        ]" :key="index">
            <div class="py-4">
                <button @click="openFAQ === index ? openFAQ = null : openFAQ = index"
                    class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-medium" x-text="faq.question"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform"
                        :class="{'rotate-180': openFAQ === index}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06 0L10 10.94l3.71-3.73a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <p x-show="openFAQ === index" x-html="faq.answer" class="mt-2 text-gray-600"></p>
            </div>
        </template>
    </div>
</div>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [{
                "@type": "Question",
                "name": "How do I upload a file?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Click on the upload area or drag and drop your files into the designated space."
                }
            },
            {
                "@type": "Question",
                "name": "What file formats are supported?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We support a wide range of formats. <a href=\"supported-files\" class=\"text-blue-600 hover:text-blue-800\">Supported files</a>."
                }
            },
            {
                "@type": "Question",
                "name": "How long does the conversion take?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Conversion times depend on the file size and format but typically take a few seconds to a minute."
                }
            },
            {
                "@type": "Question",
                "name": "Is there a file size limit?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, the maximum file size is 200MB per file."
                }
            },
            {
                "@type": "Question",
                "name": "How can I download my converted files?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Once the conversion is complete, you can click the download button next to your file."
                }
            },
            {
                "@type": "Question",
                "name": "Can I convert multiple files at once?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, you can upload multiple files and convert them simultaneously."
                }
            }
        ]
    }
</script>