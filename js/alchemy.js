import { createPublicClient, http } from "viem";
import { polygonAmoy } from "viem/chains";

const client = createPublicClient({
  chain: polygonAmoy,
  transport: http("https://polygon-amoy.g.alchemy.com/v2/nddNy-TwcMMHNocYu6WF9"),
});

const block = await client.getBlock({
  blockNumber: 123456n,
});

console.log(block);