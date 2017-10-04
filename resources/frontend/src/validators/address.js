export default function(val) {
  return (_.isEmpty(val)) ? true : /^[a-zA-Z0-9\s#,.'-]+$/.test(val)
}
