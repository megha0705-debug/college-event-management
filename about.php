<?php
/**
 * About Page
 * CampusPulse — College Event Management Website
 */

$page_title = 'About CampusPulse';
require_once __DIR__ . '/includes/header.php';
?>

<!-- About Hero -->
<div class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0a0f1d 0%, #1e1b4b 60%, #31104b 100%);">
  <div class="container py-4 text-center">
    <span class="badge bg-purple px-3 py-1 rounded-pill mb-3">Our Mission &amp; Purpose</span>
    <h1 class="display-5 fw-bold mb-3">Empowering Campus Experiences</h1>
    <p class="lead text-light mx-auto" style="max-width: 700px; opacity: 0.9;">
      CampusPulse is the college's unified digital platform connecting students, faculty coordinators, and club organizers in a vibrant hub of innovation, creativity, and athletic excellence.
    </p>
  </div>
</div>

<div class="container py-5">
  <!-- Mission & Vision Section -->
  <div class="row g-5 align-items-center mb-5">
    <div class="col-lg-6">
      <span class="text-uppercase fw-bold text-purple small">Who We Are</span>
      <h2 class="fw-bold mb-3">Reimagining How Colleges Celebrate &amp; Collaborate</h2>
      <p class="text-secondary leading-relaxed mb-3">
        CampusPulse was conceived by the College Student Council and Dean of Student Affairs to replace fragmented notice boards and manual paper registration lines with an elegant, responsive digital event ecosystem.
      </p>
      <p class="text-secondary leading-relaxed mb-4">
        Every year, our college hosts over 50 symposiums, hackathons, cultural festivals, workshops, and sports tournaments. With CampusPulse, every student has equal and instantaneous access to participate, compete, and lead.
      </p>

      <div class="row g-3">
        <div class="col-6">
          <div class="p-3 bg-light rounded-3 border">
            <h4 class="fw-bold text-navy mb-1"><i class="fas fa-check-circle text-purple me-1"></i> 100%</h4>
            <small class="text-muted">Digital Registrations &amp; QR Passes</small>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 bg-light rounded-3 border">
            <h4 class="fw-bold text-navy mb-1"><i class="fas fa-users text-purple me-1"></i> 24+</h4>
            <small class="text-muted">Active Student Clubs &amp; Societies</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card border-0 shadow-lg rounded-4 p-4 text-white position-relative overflow-hidden" 
           style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <h4 class="fw-bold mb-3 text-white"><i class="fas fa-bullseye text-warning me-2"></i> Core Pillars of CampusPulse</h4>
        
        <div class="d-flex gap-3 mb-3">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem;">
            <i class="fas fa-compass"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1 text-white">Effortless Discovery</h6>
            <small class="text-light opacity-75">Filter through technical workshops, cultural celebrations, and sports events with real-time seat availability.</small>
          </div>
        </div>

        <div class="d-flex gap-3 mb-3">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #059669, #10b981);">
            <i class="fas fa-bolt"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1 text-white">Instant One-Click Pass</h6>
            <small class="text-light opacity-75">Instant seat reservations with prevention of overbooking, and downloadable digital verification tickets.</small>
          </div>
        </div>

        <div class="d-flex gap-3">
          <div class="brand-icon flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #db2777, #f43f5e);">
            <i class="fas fa-chart-line"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1 text-white">Streamlined Faculty Administration</h6>
            <small class="text-light opacity-75">Comprehensive administrator controls to schedule events, manage caps, and export attendee rosters in CSV.</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-5 opacity-25">

  <!-- Organizing Team Section -->
  <div class="text-center mb-5">
    <span class="text-uppercase fw-bold text-purple small">The People Behind the Platform</span>
    <h2 class="fw-bold mt-1">Our Leadership &amp; Organizing Committee</h2>
    <p class="text-muted mx-auto" style="max-width: 600px;">
      Guided by dedicated faculty advisors and driven by passionate student coordinators committed to fostering vibrant campus life.
    </p>
  </div>

  <div class="row g-4 mb-5">
    <!-- Member 1 -->
    <div class="col-lg-3 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 bg-white">
        <div class="student-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.8rem; background: linear-gradient(135deg, #1e40af, #3b82f6);">
          DR
        </div>
        <h5 class="fw-bold mb-1">Dr. Eleanor Vance</h5>
        <span class="badge bg-purple-soft text-purple mb-2">Dean of Student Affairs</span>
        <p class="text-secondary small mb-3">Guiding collegiate policies, student engagement initiatives, and university-wide extracurricular activities.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fas fa-envelope"></i></a>
        </div>
      </div>
    </div>

    <!-- Member 2 -->
    <div class="col-lg-3 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 bg-white">
        <div class="student-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.8rem; background: linear-gradient(135deg, #7c3aed, #a855f7);">
          RK
        </div>
        <h5 class="fw-bold mb-1">Prof. Rajesh Kulkarni</h5>
        <span class="badge bg-primary-subtle text-primary mb-2">Faculty Convener</span>
        <p class="text-secondary small mb-3">Department of Computer Science &amp; Engineering advisor overseeing technical symposia and hackathons.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fas fa-envelope"></i></a>
        </div>
      </div>
    </div>

    <!-- Member 3 -->
    <div class="col-lg-3 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 bg-white">
        <div class="student-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.8rem; background: linear-gradient(135deg, #db2777, #f43f5e);">
          AS
        </div>
        <h5 class="fw-bold mb-1">Aarav Sharma</h5>
        <span class="badge bg-danger-subtle text-danger mb-2">Student Council President</span>
        <p class="text-secondary small mb-3">Final year CSE undergrad leading the campus event volunteers, logistics committee, and media promotions.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-github"></i></a>
        </div>
      </div>
    </div>

    <!-- Member 4 -->
    <div class="col-lg-3 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 bg-white">
        <div class="student-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.8rem; background: linear-gradient(135deg, #059669, #10b981);">
          MC
        </div>
        <h5 class="fw-bold mb-1">Meera Chandran</h5>
        <span class="badge bg-success-subtle text-success mb-2">Cultural Secretary</span>
        <p class="text-secondary small mb-3">Organizing lead for music fests, theatrical productions, dance competitions, and fine arts exhibitions.</p>
        <div class="d-flex justify-content-center gap-2">
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="btn btn-sm btn-light rounded-circle"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Frequently Asked Questions Accordion -->
  <div class="row justify-content-center mt-5">
    <div class="col-lg-8">
      <div class="text-center mb-4">
        <span class="text-uppercase fw-bold text-purple small">Need Help?</span>
        <h3 class="fw-bold">Frequently Asked Questions</h3>
      </div>

      <div class="accordion shadow-sm rounded-4 overflow-hidden" id="faqAccordion">
        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header">
            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
              Who is eligible to register for campus events?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-secondary">
              All enrolled college undergraduate, postgraduate, and doctoral students with a valid student email address and Student ID are eligible to register.
            </div>
          </div>
        </div>

        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              Can I cancel my event registration if my schedule changes?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-secondary">
              Yes! You can cancel your registration at any time prior to the event date directly from your <strong>My Registrations</strong> student dashboard. This allows other students on the waiting list to participate.
            </div>
          </div>
        </div>

        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              How do organizers verify student entry at the venue?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-secondary">
              Upon confirmed registration, your digital pass is accessible on your phone or printable from your student dashboard. Show the QR pass and your College ID at the entrance desk for instant check-in.
            </div>
          </div>
        </div>

        <div class="accordion-item border-0">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
              How can student clubs host or publish their own events?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-secondary">
              Official faculty advisors and authorized club presidents have Administrator access to publish events, upload banner visuals, set maximum seat capacities, and export attendee rosters.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

