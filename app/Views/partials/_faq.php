<div x-data="{ openFAQ: null }" class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg border rounded-lg">
    <h2 class="text-3xl font-semibold mb-6 text-gray-800">Frequently Asked Questions</h2>

    <div class="divide-y divide-gray-200">
        <template x-for="(faq, index) in [
            { question: 'How do I upload a file?', answer: 'Click on the upload area or drag and drop your files into the designated space.' },
            { question: 'How long do you retain files?', answer: 'Files are automatically removed every 30 minutes from our servers.' },
            { question: 'What file formats are supported?', answer: 'We support a wide range of formats. <a href=\'supported-files\' class=\'text-blue-600 hover:text-blue-800\'>Supported files</a>.' },
            { question: 'How long does the conversion take?', answer: 'Conversion times depend on the file size and format but typically take a few seconds to a minute.' },
            { question: 'Is there a file size limit?', answer: 'Yes, the maximum file size is 200MB per file.' },
            { question: 'How can I download my converted files?', answer: 'Once the conversion is complete, you can click the download button next to your file.' },
            { question: 'Can I convert multiple files at once?', answer: 'Yes, you can upload multiple files and convert them simultaneously.' }
        ]" :key="index">
            <div class="py-4">
                <!-- FAQ Question -->
                <button @click="openFAQ === index ? openFAQ = null : openFAQ = index"
                    @keydown.enter="openFAQ === index ? openFAQ = null : openFAQ = index"
                    class="flex justify-between items-center w-full text-left text-lg font-medium text-gray-800 hover:text-blue-600 transition">
                    <span x-text="faq.question"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-200"
                        :class="{'rotate-180': openFAQ === index}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06 0L10 10.94l3.71-3.73a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 010-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- FAQ Answer -->
                <div x-show="openFAQ === index" x-transition.duration.200ms
                    class="mt-2 text-gray-600 overflow-hidden"
                    x-ref="faqAnswer"
                    :style="openFAQ === index ? 'max-height:' + $refs.faqAnswer.scrollHeight + 'px' : 'max-height: 0px'">
                    <p class="py-2 px-4 bg-gray-50 border-l-4 border-blue-500 rounded-md" x-html="faq.answer"></p>
                </div>
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
                "name": "How long do you retain files?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Files are automatically removed every 30 minutes from our servers."
                }
            },
            {
                "@type": "Question",
                "name": "What file formats are supported?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We support a wide range of formats. <a href='supported-files' class='text-blue-600 hover:text-blue-800'>Supported files</a>."
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