import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

const faqs = [
  { q: "Is Aeterna an e-commerce brand?", a: "No. Aeterna is a brand experience. We showcase our collections and story here. For purchasing inquiries, please contact us directly." },
  { q: "Where can I view Aeterna pieces in person?", a: "Visit our flagship showrooms in Milan and Paris, or contact us for a private viewing appointment." },
  { q: "Do you offer custom tailoring?", a: "Yes. Our bespoke service is available by appointment. Reach out through our contact page to discuss your vision." },
  { q: "What materials do you use?", a: "We source premium sustainable materials including Italian wool, Japanese denim, Mongolian cashmere, and peace silk." },
  { q: "How can I collaborate with Aeterna?", a: "We welcome collaborations with aligned brands, artists, and creatives. Send us a proposal through the contact page." },
  { q: "Do you ship internationally?", a: "For private orders, we offer worldwide delivery through our concierge service. Contact us for details." },
];

const FAQPage = () => (
  <PageTransition>
    <div className="pt-28 pb-16">
      <section className="container-luxury max-w-3xl mx-auto">
        <AnimatedSection>
          <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">FAQ</h1>
          <p className="text-muted-foreground text-sm tracking-wider mb-16">Common questions answered</p>
        </AnimatedSection>

        <AnimatedSection>
          <Accordion type="single" collapsible className="space-y-2">
            {faqs.map((faq, i) => (
              <AccordionItem key={i} value={`item-${i}`} className="border-b border-border">
                <AccordionTrigger className="py-6 text-left font-display text-lg tracking-wide hover:text-accent transition-colors hover:no-underline">
                  {faq.q}
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground leading-relaxed pb-6">
                  {faq.a}
                </AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        </AnimatedSection>
      </section>
    </div>
  </PageTransition>
);

export default FAQPage;
