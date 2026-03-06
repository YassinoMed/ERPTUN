import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
  vus: 10,
  duration: "30s",
};

export default function () {
  const baseUrl = __ENV.BASE_URL || "http://localhost:8080/wms";
  const res = http.get(`${baseUrl}/wms/warehouses`);
  check(res, {
    "status is 200": (r) => r.status === 200,
  });
  sleep(1);
}
