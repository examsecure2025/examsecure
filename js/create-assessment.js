$(document).ready(function() {
    // Store questions as they're added
    let questions = {
        multiple_choice: [],
        identification: [],
        true_false: []
    };
    
    // Track current question index for each type
    let currentIndex = {
        multiple_choice: -1,
        identification: -1,
        true_false: -1
    };

    // Track if we're in edit mode
    let isEditMode = {
        multiple_choice: false,
        identification: false,
        true_false: false
    };

    // Hide delete buttons initially
    $('.mcq-delete-btn, .idq-delete-btn, .tfq-delete-btn').hide();

    // Helper para i-update kung kailan lalabas ang delete button
    function updateDeleteButton(type) {
        if (questions[type].length > 0) {
            $(`.${type}-delete-btn`).show();
        } else {
            $(`.${type}-delete-btn`).hide();
        }
    }

    // Function para i-reset ang MCQ form
    function resetMCQForm() {
        console.log('Resetting MCQ form');
        $('.mcq-question-input').val('');
        $('.mcq-points').val('1');
        $('.mcq-correct-answer').val('');
        $('.mcq-choice-input').each(function() {
            $(this).val('');
        });
        isEditMode.multiple_choice = false;
        currentIndex.multiple_choice = -1;
    }

    // Function para i-reset ang Identification form
    function resetIDForm() {
        console.log('Resetting ID form');
        $('.idq-question-input').val('');
        $('.idq-points').val('1');
        $('.idq-correct-answer').val('');
        isEditMode.identification = false;
        currentIndex.identification = -1;
    }

    // Function para i-reset ang True/False form
    function resetTFForm() {
        console.log('Resetting TF form');
        $('.tfq-question-input').val('');
        $('.tfq-points').val('1');
        $('.tfq-correct-answer').val('Choose');
        isEditMode.true_false = false;
        currentIndex.true_false = -1;
    }

    // Function para i-update ang MCQ UI
    function updateMCQUI() {
        const count = questions.multiple_choice.length;
        console.log(`Updating MCQ UI: count=${count}`);
        $('.mcq-questions-made').text(count);
        if (isEditMode.multiple_choice) {
            $('.mcq-question-number').text(currentIndex.multiple_choice + 1);
        } else {
            $('.mcq-question-number').text(count + 1);
        }
        $('.mcq-delete-btn').toggle(isEditMode.multiple_choice);
    }

    // Function para i-update ang Identification UI
    function updateIDUI() {
        const count = questions.identification.length;
        console.log(`Updating ID UI: count=${count}`);
        $('.idq-questions-made').text(count);
        if (isEditMode.identification) {
            $('.idq-question-number').text(currentIndex.identification + 1);
        } else {
            $('.idq-question-number').text(count + 1);
        }
        $('.idq-delete-btn').toggle(isEditMode.identification);
    }

    // Function para i-update ang True/False UI
    function updateTFUI() {
        const count = questions.true_false.length;
        console.log(`Updating TF UI: count=${count}`);
        $('.tfq-questions-made').text(count);
        if (isEditMode.true_false) {
            $('.tfq-question-number').text(currentIndex.true_false + 1);
        } else {
            $('.tfq-question-number').text(count + 1);
        }
        $('.tfq-delete-btn').toggle(isEditMode.true_false);
    }

    // Function para ipakita ang MCQ question
    function displayMCQQuestion(index) {
        console.log('Displaying MCQ question at index:', index);
        if (index >= 0 && index < questions.multiple_choice.length) {
            const question = questions.multiple_choice[index];
            $('.mcq-question-input').val(question.text);
            $('.mcq-points').val(question.points);
            $('.mcq-correct-answer').val(question.correct_answer);
            
            // I-set ang options
            if (question.options) {
                $('.mcq-choice-input').eq(0).val(question.options.a);
                $('.mcq-choice-input').eq(1).val(question.options.b);
                $('.mcq-choice-input').eq(2).val(question.options.c);
                $('.mcq-choice-input').eq(3).val(question.options.d);
            }
            
            $('.mcq-question-number').text(index + 1);
            isEditMode.multiple_choice = true;
            currentIndex.multiple_choice = index;
            updateMCQUI();
        } else {
            resetMCQForm();
        }
    }

    // Function para ipakita ang Identification question
    function displayIDQuestion(index) {
        console.log('Displaying ID question at index:', index);
        if (index >= 0 && index < questions.identification.length) {
            const question = questions.identification[index];
            $('.idq-question-input').val(question.text);
            $('.idq-points').val(question.points);
            $('.idq-correct-answer').val(question.correct_answer);
            $('.idq-question-number').text(index + 1);
            isEditMode.identification = true;
            currentIndex.identification = index;
            updateIDUI();
        } else {
            resetIDForm();
        }
    }

    // Function para ipakita ang True/False question
    function displayTFQuestion(index) {
        console.log('Displaying TF question at index:', index);
        if (index >= 0 && index < questions.true_false.length) {
            const question = questions.true_false[index];
            $('.tfq-question-input').val(question.text);
            $('.tfq-points').val(question.points);
            $('.tfq-correct-answer').val(question.correct_answer);
            $('.tfq-question-number').text(index + 1);
            isEditMode.true_false = true;
            currentIndex.true_false = index;
            updateTFUI();
        } else {
            resetTFForm();
        }
    }

    // Function para magdagdag ng question
    function addQuestion(type, data) {
        console.log(`Adding ${type} question:`, data);
        
        // I-push sa questions array
        questions[type].push({
            type: type,
            text: data.text,
            points: data.points,
            correct_answer: data.correct_answer,
            options: data.options // Para lang sa multiple choice
        });
        
        console.log(`Questions array after adding:`, questions[type]);
        
        // I-reset muna ang form
        resetForm(type);
        
        // Tapos i-update ang UI
        updateUI(type);
    }

    // Function para i-validate ang question data
    function validateQuestion(type, data) {
        console.log(`Validating ${type} question:`, data);
        
        // Check kung may laman ang question text
        if (!data.text || data.text.trim() === '') {
            alert('Please enter the question text');
            return false;
        }
        
        // Check kung may correct answer
        if (type === 'true_false') {
            if (!data.correct_answer || data.correct_answer === '') {
                alert('Please select True or False');
                return false;
            }
        } else if (!data.correct_answer || data.correct_answer.trim() === '') {
            alert('Please select the correct answer');
            return false;
        }
        
        // Check options para sa multiple choice
        if (type === 'multiple_choice') {
            console.log('Validating MCQ options:', data.options);
            let choiceCount = parseInt($('.mcq-choice-count').val()) || 4;
            
            for (let i = 0; i < choiceCount; i++) {
                let key = String.fromCharCode(97 + i); // a, b, c, d, ...
                if (!data.options[key] || data.options[key].trim() === '') {
                    console.log(`Missing option ${key}:`, data.options[key]);
                    alert('Please fill in all options');
                    return false;
                }
            }
        }
        
        return true;
    }

    // Handle choice count change
    $('.mcq-choice-count').on('change', function() {
        var count = parseInt($(this).val());
        var labels = ['A','B','C','D','E'];
        var $list = $(this).closest('.mcq-form-body').find('.mcq-choices-list');
        var $correctAnswer = $(this).closest('.mcq-form-body').find('.mcq-correct-answer');
        
        // Update choices list
        $list.empty();
        for (var i=0; i<count; i++) {
            $list.append('<div class="mcq-choice-row"><span class="mcq-choice-label">'+labels[i]+'</span><input type="text" class="mcq-choice-input" placeholder="Enter Choice '+labels[i]+'"></div>');
        }
        
        // Update correct answer select options
        $correctAnswer.empty();
        $correctAnswer.append('<option value="" selected disabled>Select correct answer</option>');
        for (var i=0; i<count; i++) {
            $correctAnswer.append('<option value="'+labels[i]+'">'+labels[i]+'</option>');
        }
    });

    // Initialize MCQ correct answer select
    $('.mcq-correct-answer').each(function() {
        var $select = $(this);
        var count = parseInt($select.closest('.mcq-form-body').find('.mcq-choice-count').val()) || 4;
        var labels = ['A','B','C','D','E'];
        
        $select.empty();
        $select.append('<option value="" selected disabled>Select correct answer</option>');
        for (var i=0; i<count; i++) {
            $select.append('<option value="'+labels[i]+'">'+labels[i]+'</option>');
        }
    });

    // Handle MCQ next button
    $('.mcq-next-btn').click(function() {
        console.log('MCQ Next button clicked');
        
        const options = {};
        let choiceCount = parseInt($('.mcq-choice-count').val()) || 4;
        
        for (let i = 0; i < choiceCount; i++) {
            let key = String.fromCharCode(97 + i); // a, b, c, d, e
            options[key] = $('.mcq-choice-input').eq(i).val().trim();
        }
        
        const questionData = {
            text: $('.mcq-question-input').val().trim(),
            points: $('.mcq-points').val(),
            correct_answer: $('.mcq-correct-answer').val(),
            options: options
        };
        
        if (validateQuestion('multiple_choice', questionData)) {
            if (isEditMode.multiple_choice) {
                // Update existing question
                questions.multiple_choice[currentIndex.multiple_choice] = questionData;
                // Move to next question if available
                if (currentIndex.multiple_choice < questions.multiple_choice.length - 1) {
                    currentIndex.multiple_choice++;
                    displayMCQQuestion(currentIndex.multiple_choice);
                } else {
                    resetMCQForm();
                }
            } else {
                // Add new question
                questions.multiple_choice.push(questionData);
                resetMCQForm();
            }
            updateMCQUI();
        }
    });
    
    
    $('.mcq-delete-btn').click(function() {
        if (isEditMode.multiple_choice && currentIndex.multiple_choice >= 0) {
           
            questions.multiple_choice.splice(currentIndex.multiple_choice, 1);
            
           
            if (questions.multiple_choice.length > 0) {
               
                if (currentIndex.multiple_choice >= questions.multiple_choice.length) {
                    currentIndex.multiple_choice = questions.multiple_choice.length - 1;
                }
                displayMCQQuestion(currentIndex.multiple_choice);
            } else {
               
                resetMCQForm();
            }
          
            updateMCQUI();
        }
    });

    
    $('.mcq-prev-btn').click(function() {
        console.log('MCQ Previous button clicked, current index:', currentIndex.multiple_choice);
        if (currentIndex.multiple_choice > 0) {
            currentIndex.multiple_choice--;
            displayMCQQuestion(currentIndex.multiple_choice);
            $('.mcq-question-number').text(currentIndex.multiple_choice + 1);
        } else if (questions.multiple_choice.length > 0) {
            currentIndex.multiple_choice = questions.multiple_choice.length - 1;
            displayMCQQuestion(currentIndex.multiple_choice);
            $('.mcq-question-number').text(currentIndex.multiple_choice + 1);
        }
    });

    // Handle Identification next button
    $('.idq-next-btn').click(function() {
        console.log('IDQ Next button clicked');
        
        const questionData = {
            text: $('.idq-question-input').val().trim(),
            points: $('.idq-points').val(),
            correct_answer: $('.idq-correct-answer').val().trim()
        };
        
        if (validateQuestion('identification', questionData)) {
            if (isEditMode.identification) {
                // Update existing question
                questions.identification[currentIndex.identification] = questionData;
                // Move to next question if available
                if (currentIndex.identification < questions.identification.length - 1) {
                    currentIndex.identification++;
                    displayIDQuestion(currentIndex.identification);
                } else {
                    resetIDForm();
                }
            } else {
                // Add new question
                questions.identification.push(questionData);
                resetIDForm();
            }
            updateIDUI();
        }
    });
    
    // Handle Identification delete button
    $('.idq-delete-btn').click(function() {
        if (isEditMode.identification && currentIndex.identification >= 0) {
            // Remove the question from the array
            questions.identification.splice(currentIndex.identification, 1);
            
            // If there are more questions, show the next one
            if (questions.identification.length > 0) {
                // If we deleted the last question, go to the new last question
                if (currentIndex.identification >= questions.identification.length) {
                    currentIndex.identification = questions.identification.length - 1;
                }
                displayIDQuestion(currentIndex.identification);
            } else {
                // If no questions left, reset the form
                resetIDForm();
            }
            // Update UI
            updateIDUI();
        }
    });

    // Handle Identification previous button
    $('.idq-prev-btn').click(function() {
        console.log('IDQ Previous button clicked, current index:', currentIndex.identification);
        if (currentIndex.identification > 0) {
            currentIndex.identification--;
            displayIDQuestion(currentIndex.identification);
            $('.idq-question-number').text(currentIndex.identification + 1);
        } else if (questions.identification.length > 0) {
            currentIndex.identification = questions.identification.length - 1;
            displayIDQuestion(currentIndex.identification);
            $('.idq-question-number').text(currentIndex.identification + 1);
        }
    });

    // Handle True/False next button
    $('.tfq-next-btn').click(function() {
        console.log('TFQ Next button clicked');
        
        const questionData = {
            text: $('.tfq-question-input').val().trim(),
            points: $('.tfq-points').val(),
            correct_answer: $('.tfq-correct-answer').val()
        };
        
        if (validateQuestion('true_false', questionData)) {
            if (isEditMode.true_false) {
                // Update existing question
                questions.true_false[currentIndex.true_false] = questionData;
                // Move to next question if available
                if (currentIndex.true_false < questions.true_false.length - 1) {
                    currentIndex.true_false++;
                    displayTFQuestion(currentIndex.true_false);
                } else {
                    resetTFForm();
                }
            } else {
                // Add new question
                questions.true_false.push(questionData);
                resetTFForm();
            }
            updateTFUI();
        }
    });
    
    // Handle True/False delete button
    $('.tfq-delete-btn').click(function() {
        if (isEditMode.true_false && currentIndex.true_false >= 0) {
            // Remove the question from the array
            questions.true_false.splice(currentIndex.true_false, 1);
            
            // If there are more questions, show the next one
            if (questions.true_false.length > 0) {
                // If we deleted the last question, go to the new last question
                if (currentIndex.true_false >= questions.true_false.length) {
                    currentIndex.true_false = questions.true_false.length - 1;
                }
                displayTFQuestion(currentIndex.true_false);
            } else {
                // If no questions left, reset the form
                resetTFForm();
            }
            // Update UI
            updateTFUI();
        }
    });

    // Handle True/False previous button
    $('.tfq-prev-btn').click(function() {
        console.log('TFQ Previous button clicked, current index:', currentIndex.true_false);
        if (currentIndex.true_false > 0) {
            currentIndex.true_false--;
            displayTFQuestion(currentIndex.true_false);
            $('.tfq-question-number').text(currentIndex.true_false + 1);
        } else if (questions.true_false.length > 0) {
            currentIndex.true_false = questions.true_false.length - 1;
            displayTFQuestion(currentIndex.true_false);
            $('.tfq-question-number').text(currentIndex.true_false + 1);
        }
    });

    // Initialize UI for all question types
    updateMCQUI();
    updateIDUI();
    updateTFUI();
    
    // Search functionality removed per request
    // --- Review Modal Logic ---
    function validateAssessmentDetails() {
        const requiredFields = [
            'title', 'year_course', 'section', 'course_code',
            'timer', 'status', 'school_year',
            'schedule_date', 'schedule_time',
            'close_date', 'close_time'
        ];
        for (let field of requiredFields) {
            const val = $('#' + field).val();
            if (!val || val === '' || val === 'Select timer' || val === 'Choose time') {
                alert('Please fill in all required fields');
                return false;
            }
        }
        // Date/time logic
        const schedule = new Date($('#schedule_date').val() + ' ' + $('#schedule_time').val());
        const closing = new Date($('#close_date').val() + ' ' + $('#close_time').val());
        if (closing <= schedule) {
            alert('Closing time must be after schedule time');
            return false;
        }
        return true;
    }

    function fillReviewModal() {
        // Details
        $('#review-details-grid').html(`
            <div><b>Title of the test</b><br>${$('#title').val()}</div>
            <div><b>Year & Course</b><br>${$('#year_course').val()}</div>
            <div><b>Sections</b><br>${$('#section').val()}</div>
            <div><b>Course Code</b><br>${$('#course_code').val()}</div>
            <div><b>Timer</b><br>${$('#timer').val()}</div>
            <div><b>Status</b><br>${$('#status').val()}</div>
            <div><b>School Year</b><br>${$('#school_year').val()}</div>
            <div><b>Schedule</b><br>${$('#schedule_date').val()} ${$('#schedule_time').val()}</div>
            <div><b>Automatically Close Assessment</b><br>${$('#close_date').val()} ${$('#close_time').val()}</div>
        `);
        
        $('#review-mcq-shuffle').text('Shuffle Questions : ' + $('.mcq-shuffle').val().toUpperCase());
        $('#review-mcq-count').text('Number Of Questions : ' + questions.multiple_choice.length);
        let mcqHtml = '';
        questions.multiple_choice.forEach((q, i) => {
            mcqHtml += `<div style="background:#17305c;border-radius:1rem;padding:1.2rem 1.5rem;margin-bottom:1.2rem;">
                <div style="display:flex;align-items:center;gap:1.2rem;">
                    <div style="font-size:2.2rem;font-weight:700;color:#ffe600;">${i+1}</div>
                    <div style="flex:1;">
                        <div style="font-size:1.15rem;font-weight:600;color:#fff;">${q.text}</div>
                        <div style="margin-top:0.7rem;">
                            ${Object.entries(q.options).map(([key, val]) => {
                                let correct = (q.correct_answer && key.toUpperCase() === q.correct_answer.toUpperCase());
                                return `<span style='font-weight:700;color:${correct ? "#2ecc40" : "#fff"};margin-right:1.5rem;'>${key.toUpperCase()}. ${val}</span>`;
                            }).join('')}
                        </div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#ffe600;white-space:nowrap;">${q.points} point${q.points > 1 ? 's' : ''}</div>
                </div>
            </div>`;
        });
        $('#review-mcq-list').html(mcqHtml);
      
        $('#review-id-shuffle').text('Shuffle Questions : ' + $('.idq-shuffle').val().toUpperCase());
        $('#review-id-ai').text('Auto check student answer using AI : ' + $('.idq-ai-check').val().toUpperCase());
        $('#review-id-count').text('Number Of Questions : ' + questions.identification.length);
        let idHtml = '';
        questions.identification.forEach((q, i) => {
            idHtml += `<div style="background:#17305c;border-radius:1rem;padding:1.2rem 1.5rem;margin-bottom:1.2rem;">
                <div style="display:flex;align-items:center;gap:1.2rem;">
                    <div style="font-size:2.2rem;font-weight:700;color:#ffe600;">${i+1}</div>
                    <div style="flex:1;">
                        <div style="font-size:1.15rem;font-weight:600;color:#fff;">${q.text}</div>
                        <div style="margin-top:0.7rem;font-weight:700;color:#2ecc40;">Correct Answer : ${q.correct_answer}</div>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#ffe600;white-space:nowrap;">${q.points} point${q.points > 1 ? 's' : ''}</div>
                </div>
            </div>`;
        });
        $('#review-id-list').html(idHtml);
       
        if (questions.true_false.length > 0) {
            $('#review-tf-title, #review-tf-meta, #review-tf-list').show();
            $('#review-tf-shuffle').text('Shuffle Questions : ' + $('.tfq-shuffle').val().toUpperCase());
            $('#review-tf-count').text('Number Of Questions : ' + questions.true_false.length);
            let tfHtml = '';
            questions.true_false.forEach((q, i) => {
                tfHtml += `<div style="background:#17305c;border-radius:1rem;padding:1.2rem 1.5rem;margin-bottom:1.2rem;">
                    <div style="display:flex;align-items:center;gap:1.2rem;">
                        <div style="font-size:2.2rem;font-weight:700;color:#ffe600;">${i+1}</div>
                        <div style="flex:1;">
                            <div style="font-size:1.15rem;font-weight:600;color:#fff;">${q.text}</div>
                            <div style="margin-top:0.7rem;font-weight:700;color:#2ecc40;">Correct Answer : ${q.correct_answer}</div>
                        </div>
                        <div style="font-size:1.1rem;font-weight:700;color:#ffe600;white-space:nowrap;">${q.points} point${q.points > 1 ? 's' : ''}</div>
                    </div>
                </div>`;
            });
            $('#review-tf-list').html(tfHtml);
        } else {
            $('#review-tf-title, #review-tf-meta, #review-tf-list').hide();
        }
    }

    $('#reviewAssessmentBtn').click(function(e) {
        e.preventDefault();
        if (!validateAssessmentDetails()) return;
        fillReviewModal();
        $('#reviewModal').show();
    });

    $('#closeReviewModal').click(function() {
        $('#reviewModal').hide();
    });

    
    $('#publishAssessmentBtn').click(function(e) {
        e.preventDefault();
        
        if (!validateAssessmentDetails()) {
            return;
        }

      
        const $publishBtn = $(this);
        const originalText = $publishBtn.text();
        $publishBtn.text('Publishing...').prop('disabled', true);

        
        const formattedQuestions = [
            ...questions.multiple_choice.map(q => ({
                type: 'multiple_choice',
                text: q.text,
                points: parseInt(q.points),
                correct_answer: q.correct_answer,
                options: q.options
            })),
            ...questions.identification.map(q => ({
                type: 'identification',
                text: q.text,
                points: parseInt(q.points),
                correct_answer: q.correct_answer
            })),
            ...questions.true_false.map(q => ({
                type: 'true_false',
                text: q.text,
                points: parseInt(q.points),
                correct_answer: q.correct_answer
            }))
        ];

        const formData = {
            title: $('#title').val(),
            year_course: $('#year_course').val(),
            sections: $('#section').val(),
            course_code: $('#course_code').val(),
            timer: $('#timer').val(),
            status: $('#status').val(),
            school_year: $('#school_year').val(),
            schedule_date: $('#schedule_date').val(),
            schedule_time: $('#schedule_time').val(),
            close_date: $('#close_date').val(),
            close_time: $('#close_time').val(),
            shuffle_mcq: $('.mcq-shuffle').val() === 'On',
            shuffle_identification: $('.idq-shuffle').val() === 'On',
            shuffle_true_false: $('.tfq-shuffle').val() === 'On',
            ai_check_identification: $('.idq-ai-check').val() === 'On',
            questions: formattedQuestions
        };

       
        console.log('Sending data to server:', JSON.stringify(formData, null, 2));

        $.ajax({
            url: 'auth/processAssessment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                if (response.status === 'success') {
                    // Show the publish success modal
                    $('#successAccessCode').text(response.data.access_code);
                    $('#publishSuccessModal').show();
                    $('#reviewModal').hide();
                    $('#goToDashboardBtn').off('click').on('click', function() {
                        window.location.href = 'dashboard.php';
                    });
                } else {
                    alert('Error: ' + response.message);
                    $publishBtn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
           
                console.log('Raw server response:', xhr.responseText);
                console.log('Response status:', xhr.status);
                console.log('Response status text:', xhr.statusText);
                console.log('Error status:', status);
                console.log('Error message:', error);
                
                let errorMessage = 'Error creating assessment. ';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage += response.message || error;
                    if (response.debug) {
                        console.log('Debug information:', response.debug);
                    }
                } catch (e) {
                    console.log('JSON parse error:', e);
                    errorMessage += 'Server returned invalid JSON response. ';
                    errorMessage += error;
                }
                
                alert(errorMessage);
                $publishBtn.text(originalText).prop('disabled', false);
            }
        });
    });

   
    $('#publishSuccessModal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });
}); 